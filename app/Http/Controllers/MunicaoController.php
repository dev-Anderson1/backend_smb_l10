<?php

namespace App\Http\Controllers;

use App\Models\Municao;
use Illuminate\Http\Request;

class MunicaoController extends Controller
{
    // Lista todas as munições
   public function index()
{
    return response()->json(Municao::all());
}

public function store(Request $request)
{
    $validated = $request->validate([
        'tipo' => 'required|string|max:255',
        'calibre_id' => 'required|exists:calibres,id',
        'quantidade' => 'required|integer|min:1',
    ]);

    $municao = Municao::create($validated);

    return response()->json([
        'message' => 'Munição criada com sucesso!',
        'data' => $municao
    ], 201);
}

public function show($id)
{
    return response()->json(Municao::findOrFail($id));
}

public function update(Request $request, $id)
{
    $validated = $request->validate([
        'tipo' => 'required|string|max:255',
        'calibre_id' => 'required|exists:calibres,id',
        'quantidade' => 'required|integer|min:1',
    ]);

    $municao = Municao::findOrFail($id);

    $municao->update($validated);

    return response()->json([
        'message' => 'Munição atualizada com sucesso!',
        'data' => $municao
    ]);
}

public function destroy($id)
{
    $municao = Municao::findOrFail($id);
    $municao->delete();

    return response()->json([
        'message' => 'Munição deletada com sucesso!'
    ], 200);
}

}
