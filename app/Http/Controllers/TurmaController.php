<?php

namespace App\Http\Controllers;

use App\Models\Turma;
use Illuminate\Http\Request;

class TurmaController extends Controller
{
    public function index()
{
    return response()->json(
        Turma::orderBy('tipo_curso')
             ->orderBy('nome')
             ->get()
    );
}


    public function store(Request $request)
    {
       $data = $request->validate([
    'tipo_curso' => 'required|string|max:255',
    'nome' => 'required|string|max:255',
]);

$turma = Turma::create($data);
        return response()->json($turma);
    }

     public function update(Request $request, $id)
{
    $turma = Turma::findOrFail($id);
    $turma->update($request->only('nome', 'tipo_curso'));

    return response()->json(['message' => 'Turma atualizada com sucesso']);
}

public function destroy($id)
{
    $turma = Turma::findOrFail($id);
    $turma->delete();

    return response()->json(['message' => 'Turma excluída com sucesso']);
}
}
