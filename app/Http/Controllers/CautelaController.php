<?php

namespace App\Http\Controllers;

use Log;
use App\Models\User;
use App\Models\Cautela;
use App\Models\CautelaItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class CautelaController extends Controller
{

    // Listar todas as cautelas
 public function index()
{
    $user = auth()->user();

    // Atualiza status atrasado
    Cautela::where('status', 'pendente')
        ->where('created_at', '<', now()->subHours(24))
        ->update(['status' => 'atrasado']);

    // Query base
    $query = Cautela::with([
        'admin:id,name,apelido',
        'usuario:id,name,apelido,email',
        'itens.arma:id,modelo_id,numero_serie,quantidade_carregadores,situacao',
        'itens:id,cautela_id,arma_id,colete_id,espada_id,algema_id,outros_materiais,quantidade',
        'itens.arma.modelo:id,name',
        'itens.colete:id,tipo,num_serie',
        'itens.algema:id,tipo,num_serie',
        'itens.espada:id,tipo,num_serie',
    ]);

    // 🔥 FILTRO CORRETO PARA USUÁRIO COMUM
    if (!$user->is_admin) {
        $query->where('user_id', $user->id); // ← AGORA ESTÁ CORRETO!
    }

    $cautelas = $query->get();

    return response()->json($cautelas);
}




public function show($id)
{
    $user = auth()->user();

    $cautela = Cautela::with([
        'admin:id,name,apelido,email',
        'usuario:id,name,apelido,email',
        'userConfirm:id,name,apelido,email',
        'devolvidoPor:id,name,apelido,email',
        'itens:id,cautela_id,arma_id,colete_id,espada_id,algema_id,outros_materiais,quantidade',
        'itens.arma:id,modelo_id,numero_serie,quantidade_carregadores,situacao',
        'itens.arma.modelo:id,name',
        'itens.colete:id,tipo,num_serie',
        'itens.algema:id,tipo,num_serie',
        'itens.espada:id,tipo,num_serie',
    ])->find($id);

    if (!$cautela) {
        return response()->json(['message' => 'Cautela não encontrada'], 404);
    }

    // 🔥 REGRA DE ACESSO CORRETA
    if (!$user->is_admin && $cautela->user_id !== $user->id) {
        return response()->json(['message' => 'Acesso negado'], 403);
    }

    return response()->json($cautela);
}






// Atualizar status ou informações
public function update(Request $request, $id)
{
    $cautela = Cautela::find($id);

    if (!$cautela) {
        return response()->json(['message' => 'Cautela não encontrada'], 404);
    }

    $cautela->update($request->only(['status'])); // ou outros campos que quiser permitir

    return response()->json(['message' => 'Cautela atualizada', 'cautela' => $cautela]);
}

// Deletar cautela
public function destroy($id)
{
    $cautela = Cautela::find($id);

    if (!$cautela) {
        return response()->json(['message' => 'Cautela não encontrada'], 404);
    }

    $cautela->delete();

    return response()->json(['message' => 'Cautela deletada com sucesso']);
}

      

public function store(Request $request)
{
    // 🔹 1. Autentica o policial que confirma a cautela
    $request->validate([
        'email' => 'required|email',
        'password' => 'required|string',
    ]);

    $user = User::where('email', $request->email)->first();

    if (!$user || !\Hash::check($request->password, $user->password)) {
        return response()->json(['message' => 'Credenciais inválidas do policial.'], 401);
    }

    Log::info('Store Cautela iniciado', ['request' => $request->all()]);

    // 🔹 2. Validação dos campos da cautela e itens
    $request->validate([
        'admin_id' => 'required|exists:users,id',
        'user_id' => 'required|exists:users,id',

        'itens.armas' => 'nullable|array',
        'itens.armas.*.arma_id' => 'nullable|exists:armas,id',
        'itens.armas.*.quantidade' => 'nullable|integer|min:1',

        'itens.coletes' => 'nullable|array',
        'itens.coletes.*.colete_id' => 'nullable|exists:coletes,id',
        'itens.coletes.*.quantidade' => 'nullable|integer|min:1',

        'itens.algemas' => 'nullable|array',
        'itens.algemas.*.algema_id' => 'nullable|exists:algemas,id',
        'itens.algemas.*.quantidade' => 'nullable|integer|min:1',

        'itens.espadas' => 'nullable|array',
        'itens.espadas.*.espada_id' => 'nullable|exists:espadas,id',
        'itens.espadas.*.quantidade' => 'nullable|integer|min:1',

        'itens.outros' => 'nullable|array',
        'itens.outros.*.outros_materiais' => 'nullable|string|max:255',
        'itens.outros.*.quantidade' => 'nullable|integer|min:1',
    ]);

    // 🔹 3. Cria a cautela principal
    $cautela = Cautela::create([
        'admin_id' => $request->admin_id,      // quem cadastrou (admin)
        'user_id' => $request->user_id,        // policial dono
        'user_confirm_id' => $user->id,        // policial que confirmou via senha
        'status' => 'pendente',
    ]);

    Log::info('Cautela criada', ['cautela_id' => $cautela->id]);

    // 🔹 4. Função genérica para criar qualquer tipo de item
    $criarItens = function ($itens, $campoId = null) use ($cautela) {
        foreach ($itens as $item) {
            $dados = [
                'quantidade' => $item['quantidade'] ?? 1,
                'outros_materiais' => $item['outros_materiais'] ?? null,
            ];

            if ($campoId && isset($item[$campoId])) {
                $dados[$campoId] = $item[$campoId];
            }

            $cautela->itens()->create($dados);
        }
    };

    // 🔹 5. Cria cada grupo de itens (somente se tiver algo)
    if ($request->filled('itens.armas')) $criarItens($request->input('itens.armas'), 'arma_id');
    if ($request->filled('itens.coletes')) $criarItens($request->input('itens.coletes'), 'colete_id');
    if ($request->filled('itens.algemas')) $criarItens($request->input('itens.algemas'), 'algema_id');
    if ($request->filled('itens.espadas')) $criarItens($request->input('itens.espadas'), 'espada_id');
    if ($request->filled('itens.outros')) $criarItens($request->input('itens.outros'));

    Log::info('Itens criados com sucesso');

    // 🔹 6. Retorna a resposta completa
    return response()->json([
        'success' => true,
        'message' => 'Cautela criada com sucesso',
        'cautela_id' => $cautela->id,
        'created_at' => $cautela->created_at->toISOString(),
    ]);
}




    // Autenticação do usuário para finalizar a cautela
   public function finalizar(Request $request)
{
    $request->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);

    // Autentica o usuário
   

    $user = Auth::user();

    // Busca a cautela pendente dele
    $cautela = Cautela::where('user_id', $user->id)->where('status', 'pendente')->first();

    if (!$cautela) {
        return response()->json(['message' => 'Nenhuma cautela pendente encontrada para este usuário.'], 404);
    }

    // Atualiza status e salva quem finalizou (user_confirm_id)
    $cautela->update([
        'status' => 'autorizada',
        'user_confirm_id' => $user->id,
    ]);

    return response()->json(['message' => 'Cautela finalizada com sucesso.']);
}



    // Devolução dos itens com nova autenticação do usuário
    public function devolucao(Request $request)
    {
        $credentials = $request->only('email', 'password');
    
        if (!Auth::attempt($credentials)) {
            return response()->json(['message' => 'Autenticação inválida'], 401);
        }
    
        $cautela = Cautela::findOrFail($request->cautela_id);
    
        // Garantir que a cautela esteja no status 'autorizada' para poder fazer a devolução
        if ($cautela->status !== 'autorizada') {
            return response()->json(['message' => 'A cautela não foi autorizada ainda.'], 400);
        }
    
        $cautela->update(['status' => 'devolvido']);
    
        return response()->json(['message' => 'Itens devolvidos com sucesso']);
    }

    public function usuariosComCautelasPendentes()
    {
        $usuarios = User::whereHas('cautelas', function ($query) {
            $query->where('status', 'pendente');
           
        })
        ->with([
            'opm:id,bpm',
            'postoGraduacao:id,nome',
            'cautelas' => function ($query) {
                $query->where('status', 'pendente')->with([
                    'itens.arma:id,modelo_id,quantidade_carregadores',
                    'itens.arma.modelo:id,name,calibre_id',
                    'itens.arma.modelo.calibre:id,nome',
                    'itens.arma.quantidade_carregadores:id,capacidade,quantidade',
                    'itens.arma.municao:id,tipo,calibre_id,quantidade',
                    'itens.arma.municao.calibre:id,nome',
                    'itens.algema:id,tipo,num_serie,quantidade',
                    'itens.colete:id,tipo,num_serie,quantidade',
                    'itens.espada:id,tipo,num_serie,quantidade',
                ]);
            }
        ])
        ->get(['id', 'name', 'email', 'apelido', 'opm_id', 'posto_graduacoes_id']);
    
        return response()->json($usuarios);
    }

    public function getCautelasPorUsuario($userId)
    {
        if (!auth()->user()->is_admin) {
            return response()->json(['message' => 'Acesso não autorizado'], 403);
        }
    
        $cautelas = Cautela::where('user_id', $userId)
            ->where('status', 'pendente')
            ->with([
                'itens.arma:id,modelo_id,quantidade_carregadores',
                'itens.arma.modelo:id,name,calibre_id',
                'itens.arma.modelo.calibre:id,nome',
                'itens.arma.quantidade_carregadores:id,capacidade,quantidade',
                'itens.arma.municao:id,tipo,calibre_id,quantidade',
                'itens.arma.municao.calibre:id,nome',
                'itens.algema:id,tipo,num_serie,quantidade',
                'itens.colete:id,tipo,num_serie,quantidade',
                'itens.espada:id,tipo,num_serie,quantidade',
                'user:id,name,email,apelido,opm_id,posto_graduacoes_id',
                'user.opm:id,bpm',
                'user.postoGraduacao:id,nome',
            ])
            ->get();
    
        return response()->json($cautelas);
    }
    
    
    
    public function cautelasPendentesDoUsuarioAutenticado()
    {
        $user = auth()->user();
    
        // Se for admin, bloqueia
        if ($user->is_admin) {
            return response()->json(['message' => 'Acesso não autorizado'], 403);
        }
    
        // Busca as cautelas pendentes do próprio usuário
        $cautelas = Cautela::where('user_id', $user->id)
            ->where('status', 'pendente')
            ->with([
                'itens.arma:id,modelo_id,quantidade_carregadores',
                'itens.arma.modelo:id,name,calibre_id',
                'itens.arma.modelo.calibre:id,nome',
                'itens.arma.quantidade_carregadores:id,capacidade,quantidade',
                'itens.arma.municao:id,tipo,calibre_id,quantidade',
                'itens.arma.municao.calibre:id,nome',
                'itens.algema:id,tipo,num_serie,quantidade',
                'itens.colete:id,tipo,num_serie,quantidade',
                'itens.espada:id,tipo,num_serie,quantidade',
            ])
            ->get();
    
        return response()->json($cautelas);
    }

// No backend, no CautelaController
public function authUser(Request $request)
{
    $request->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);

    if (!Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
        return response()->json([
            'success' => false,
            'message' => 'Credenciais inválidas'
        ], 401);
    }

    $user = Auth::user();

    return response()->json([
        'success' => true,
        'user' => $user
    ]);
}


    public function devolverItem(Request $request, $id)
{
    $request->validate([
        'item_id' => 'required|exists:cautela_items,id'
    ]);

    $item = CautelaItem::where('cautela_id', $id)->find($request->item_id);

    if (!$item) {
        return response()->json(['message' => 'Item não encontrado'], 404);
    }

    $item->delete();

    return response()->json(['message' => 'Item devolvido com sucesso']);
}


public function devolverTodos(Request $request, $id)
{
    $cautela = Cautela::with('itens')->find($id);

    if (!$cautela) {
        return response()->json(['message' => 'Cautela não encontrada'], 404);
    }

    $request->validate([
        'email' => 'required|email',
        'password' => 'required|string',
    ]);

    $admin = User::where('email', $request->email)->first();

    if (!$admin || !\Hash::check($request->password, $admin->password)) {
        return response()->json(['message' => 'Credenciais inválidas do administrador.'], 401);
    }

    // CORREÇÃO AQUI 👇
    $cautela->update([
        'status' => 'devolvido',
        'devolvido_por_id' => $admin->id,
    ]);

    foreach ($cautela->itens as $item) {
        $item->delete();
    }

    return response()->json([
        'success' => true,
        'message' => 'Itens devolvidos com sucesso.',
        'recebido_por' => $admin->apelido ?? $admin->name,
        'devolvido_por_id' => $admin->id,
        'cautela_id' => $cautela->id,
    ]);
}







    
}
