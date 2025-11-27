<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\MunicaoSaldo;
use App\Models\Municao;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InstrutorSaldoController extends Controller
{
    // Admin: lista todos os instrutores com soma dos saldos (opcional breakdown)
    public function all(Request $request)
    {
        $user = $request->user();
        if (!$user || !$user->is_admin) return response()->json(['message' => 'Forbidden'], 403);

        $instrutores = User::where('is_instrutor', 1)
            ->where('is_obsolete', 0)
            ->select('id', 'name', 'apelido', 'opm_id', 'posto_graduacoes_id')
            ->with(['opm:id,bpm','postoGraduacao:id,nome'])
            ->get();

        // agregar saldos por instrutor
        $saldos = MunicaoSaldo::selectRaw('user_id, SUM(quantidade) as total')
            ->groupBy('user_id')
            ->pluck('total', 'user_id');

        $result = $instrutores->map(function ($inst) use ($saldos) {
            return [
                'user' => $inst,
                'total' => (int) ($saldos[$inst->id] ?? 0),
            ];
        });

        return response()->json($result);
    }

    // Mostra saldos detalhados de um instrutor (por munição, turma e tipo de aula)
    // Mostra saldos detalhados de um instrutor (cada linha individual)
public function index($id, Request $request)
{
    $user = $request->user();

    // admin pode ver qualquer, instrutor só o próprio
    if (!$user) return response()->json(['message' => 'Unauthorized'], 401);
    if (!$user->is_admin && $user->id != $id) return response()->json(['message' => 'Forbidden'], 403);

    // retorna cada registro de saldo SEM agrupamento
    $saldos = MunicaoSaldo::with([
            'municao.calibre',
            'turma',
            'tipoAula'
        ])
        ->where('user_id', $id)
        ->orderBy('id', 'desc')
        ->get();

    return response()->json($saldos);
}


    // adiciona/atualiza saldo para um instrutor (admin)
   public function adicionarSaldo($id, Request $request)
{
    $user = $request->user();
    if (!$user || !$user->is_admin) return response()->json(['message' => 'Forbidden'], 403);

    $data = $request->validate([
        'municao_id' => 'required|exists:municoes,id',
        'quantidade' => 'required|integer|min:1',
        'turma_id' => 'nullable|exists:turmas,id',
        'tipo_aula_id' => 'nullable|exists:tipo_aulas,id',
    ]);

    return DB::transaction(function () use ($id, $data) {

        // 1️⃣ BUSCA A MUNIÇÃO NO ESTOQUE
        $municao = Municao::findOrFail($data['municao_id']);

        // 2️⃣ VERIFICA SE TEM ESTOQUE SUFICIENTE
        if ($municao->quantidade < $data['quantidade']) {
            return response()->json([
                'message' => 'Estoque insuficiente! Existem apenas ' . $municao->quantidade . ' unidades.'
            ], 422);
        }

        // 3️⃣ DESCONTA DO ESTOQUE
        $municao->quantidade -= $data['quantidade'];
        $municao->save();

        // 4️⃣ ADICIONA NO SALDO DO INSTRUTOR
        $saldo = MunicaoSaldo::firstOrCreate(
            [
                'user_id' => $id,
                'municao_id' => $data['municao_id'],
                'turma_id' => $data['turma_id'] ?? null,
                'tipo_aula_id' => $data['tipo_aula_id'] ?? null,
            ],
            ['quantidade' => 0]
        );

        $saldo->quantidade += (int) $data['quantidade'];
        $saldo->save();

        return response()->json($saldo);
    });
}


public function adicionarSaldoGlobal(Request $request)
{
    $user = $request->user();
    if (!$user || !$user->is_admin) {
        return response()->json(['message' => 'Forbidden'], 403);
    }

    $data = $request->validate([
        'user_id' => 'required|exists:users,id',
        'municao_id' => 'required|exists:municoes,id',
        'quantidade' => 'required|integer|min:1',
        'turma_id' => 'nullable|exists:turmas,id',
        'tipo_aula_id' => 'nullable|exists:tipo_aulas,id',
    ]);

    return DB::transaction(function () use ($data) {
        $saldo = MunicaoSaldo::firstOrCreate(
            [
                'user_id' => $data['user_id'],
                'municao_id' => $data['municao_id'],
                'turma_id' => $data['turma_id'] ?? null,
                'tipo_aula_id' => $data['tipo_aula_id'] ?? null,
            ],
            ['quantidade' => 0]
        );

        $saldo->quantidade += (int) $data['quantidade'];
        $saldo->save();

        return response()->json($saldo);
    });
}



    // meus saldos (instrutor autenticado)
    public function meusSaldos(Request $request)
    {
        $user = $request->user();
        if (!$user) return response()->json(['message' => 'Unauthorized'], 401);

        return $this->index($user->id, $request);
    }


    public function updateSaldo($saldoId, Request $request)
{
    $user = $request->user();
    if (!$user || !$user->is_admin) {
        return response()->json(['message' => 'Forbidden'], 403);
    }

    $data = $request->validate([
        'quantidade' => 'required|integer|min:0',
        'municao_id' => 'required|exists:municoes,id',
        'turma_id' => 'nullable|exists:turmas,id',
        'tipo_aula_id' => 'nullable|exists:tipo_aulas,id',
    ]);

    return DB::transaction(function () use ($saldoId, $data) {

        $saldo = MunicaoSaldo::findOrFail($saldoId);

        // 🔎 Munição antiga
        $municaoAntiga = \App\Models\Municao::findOrFail($saldo->municao_id);

        // 🔎 Munição nova (caso tenha trocado)
        $municaoNova = \App\Models\Municao::findOrFail($data['municao_id']);

        // ---------------------------------------
        // 1️⃣ Caso tenha trocado o tipo de munição
        // ---------------------------------------
        if ($saldo->municao_id != $data['municao_id']) {

            // Devolve tudo ao estoque antigo
            $municaoAntiga->quantidade += $saldo->quantidade;
            $municaoAntiga->save();

            // Precisa ter estoque na munição nova
            if ($municaoNova->quantidade < $data['quantidade']) {
                return response()->json([
                    'message' => 'Estoque insuficiente para a nova munição selecionada.'
                ], 422);
            }

            // Debita do estoque novo
            $municaoNova->quantidade -= $data['quantidade'];
            $municaoNova->save();
        } 
        else {

            // ---------------------------------------
            // 2️⃣ Mesmo tipo de munição → ajustar diferença
            // ---------------------------------------
            $diferenca = $data['quantidade'] - $saldo->quantidade;

            if ($diferenca > 0) {
                // está aumentando → precisa debitar do estoque
                if ($municaoAntiga->quantidade < $diferenca) {
                    return response()->json([
                        'message' => "Estoque insuficiente! Restam apenas {$municaoAntiga->quantidade} unidades."
                    ], 422);
                }

                $municaoAntiga->quantidade -= $diferenca;
                $municaoAntiga->save();
            } else {
                // está reduzindo → deve devolver ao estoque
                $municaoAntiga->quantidade += abs($diferenca);
                $municaoAntiga->save();
            }
        }

        // ---------------------------------------
        // 3️⃣ Atualiza saldo do instrutor
        // ---------------------------------------
        $saldo->update([
            'quantidade' => $data['quantidade'],
            'municao_id' => $data['municao_id'],
            'turma_id' => $data['turma_id'],
            'tipo_aula_id' => $data['tipo_aula_id'],
        ]);

        return response()->json($saldo);
    });
}






}

