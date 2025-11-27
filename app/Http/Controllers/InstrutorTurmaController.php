<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Turma;
use Illuminate\Http\Request;

class InstrutorTurmaController extends Controller
{
    public function index($id)
    {
        $instrutor = User::where('is_instrutor', 1)->findOrFail($id);
        return $instrutor->turmas;
    }

    public function store(Request $request, $id)
    {
        $request->validate([
            'turma_id' => 'required|exists:turmas,id'
        ]);

        $instrutor = User::where('is_instrutor', 1)->findOrFail($id);
        $instrutor->turmas()->attach($request->turma_id);

        return response()->json(['message' => 'Turma vinculada']);
    }

    public function destroy($id, $turmaId)
    {
        $instrutor = User::where('is_instrutor', 1)->findOrFail($id);
        $instrutor->turmas()->detach($turmaId);

        return response()->json(['message' => 'Turma removida']);
    }
}
