<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class InstrutorController extends Controller
{
    public function index()
    {
        return User::where('is_instrutor', 1)
                   ->where('is_obsolete', 0)
                   ->select('id','name','email','apelido')
                   ->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'user_id' => 'required|exists:users,id'
        ]);

        $user = User::findOrFail($data['user_id']);
        $user->is_instrutor = 1;
        $user->save();

        return response()->json(['message' => 'Instrutor ativado com sucesso']);
    }

    public function update(Request $request, $id)
    {
        $instrutor = User::where('is_instrutor', 1)->findOrFail($id);

        $instrutor->update([
            'apelido' => $request->apelido ?? $instrutor->apelido,
        ]);

        return response()->json(['message' => 'Instrutor atualizado']);
    }

    public function destroy($id)
    {
        $instrutor = User::where('is_instrutor', 1)->findOrFail($id);
        $instrutor->is_instrutor = 0;
        $instrutor->save();

        return response()->json(['message' => 'Instrutor removido']);
    }
}
