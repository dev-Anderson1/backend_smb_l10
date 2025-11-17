<?php

namespace App\Http\Controllers;

use App\Models\Arma;
use App\Models\Municao;
use App\Models\Carregador;
use App\Models\ModeloArma;
use Illuminate\Http\Request;

class ArmaController extends Controller
{
    // Lista todas as armas
  public function index()
{
    // 🔹 Carrega as armas com o relacionamento 'modelo'
        $armas = Arma::with('modelo')->get();

    // 🔹 Retorna o JSON completo pro frontend
    return response()->json($armas);
}



    // Cria uma nova arma
   public function store(Request $request)
{
    $request->validate([
        'modelo_id' => 'required|exists:modelo_armas,id',
        'quantidade_carregadores' => 'required|integer|min:1',
        'numero_serie' => 'required|string|unique:armas,numero_serie',
    ]);

    $arma = Arma::create([
        'modelo_id' => $request->modelo_id,
        'quantidade_carregadores' => $request->quantidade_carregadores,
        'numero_serie' => $request->numero_serie,
        'situacao' => 'disponível',
    ]);

    return response()->json([
        'message' => 'Arma cadastrada com sucesso!',
        'data' => $arma
    ], 201);
}



    // Mostra uma arma específica
    public function show($id)
{
    $cautela = Cautela::with([
        'admin:id,name',
        'usuario:id,name,email',

        // itens da cautela
        'itens:id,cautela_id,arma_id,colete_id,espada_id,algema_id,outros_materiais,quantidade',

        // relacionamentos corretos
        'itens.arma:id,modelo_id,numero_serie,quantidade_carregadores,situacao',
        'itens.arma.modelo:id,name',

        'itens.colete:id,tipo,num_serie,quantidade',
        'itens.algema:id,tipo,num_serie,quantidade',
        'itens.espada:id,tipo,num_serie,quantidade',
    ])
    ->find($id);

    if (!$cautela) {
        return response()->json(['message' => 'Cautela não encontrada'], 404);
    }

    return response()->json($cautela);
}

    // Atualiza uma arma
   public function update(Request $request, $id)
{
    $request->validate([
        'modelo_id' => 'required|exists:modelo_armas,id',
        'numero_serie' => 'nullable|string|max:255',
        'quantidade_carregadores' => 'required|integer|min:1',
    ]);

    $arma = Arma::findOrFail($id);

    // Verifica se a quantidade de carregadores foi alterada
    if ($arma->quantidade_carregadores != $request->quantidade_carregadores) {
        $diferenca = $request->quantidade_carregadores - $arma->quantidade_carregadores;

        // Atualiza a quantidade total de carregadores
        if ($diferenca > 0) {
            \App\Models\Carregador::increment('quantidade', $diferenca);
        } elseif ($diferenca < 0) {
            \App\Models\Carregador::decrement('quantidade', abs($diferenca));
        }
    }

    // Atualiza os dados da arma
    $arma->update([
        'modelo_id' => $request->modelo_id,
        'numero_serie' => $request->numero_serie,
        'quantidade_carregadores' => $request->quantidade_carregadores,
    ]);

    return response()->json([
        'message' => 'Arma atualizada com sucesso!',
        'data' => $arma
    ]);
}


    // Deleta uma arma
   public function destroy($id)
{
    $arma = Arma::findOrFail($id);
    $carregador = Carregador::find($arma->quantidade_carregadores);

    if ($carregador) {
        $carregador->quantidade = max(0, $carregador->quantidade - 1);
        $carregador->save();
    }

    $arma->delete();

    return response()->json([
        'message' => 'Arma excluída e quantidade de carregadores ajustada.'
    ], 204);
}




    public function armasDisponiveis()
{
    // IDs de armas que estão em cautelas pendentes
    $armasIndisponiveis = \DB::table('cautela_items')
        ->join('cautelas', 'cautelas.id', '=', 'cautela_items.cautela_id')
        ->where('cautelas.status', 'pendente')
        ->pluck('cautela_items.arma_id');

    // Armas que NÃO estão nas cautelas pendentes
    $armasDisponiveis = Arma::with('modelo')
        ->whereNotIn('id', $armasIndisponiveis)
        ->get();

    return response()->json($armasDisponiveis);
}




}
