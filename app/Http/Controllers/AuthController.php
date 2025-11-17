<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\User;

class AuthController extends Controller
{
    /**
     * Login de usuário via Laravel Passport
     */
    public function login(Request $request)
    {
        // 🔹 Validação simples dos campos
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dados inválidos',
                'errors'  => $validator->errors(),
            ], 422);
        }

        // 🔹 Tenta autenticar
        $credentials = $request->only('email', 'password');

        if (!Auth::attempt($credentials)) {
            return response()->json([
                'success' => false,
                'message' => 'Credenciais incorretas. Verifique seu email e senha.',
            ], 401);
        }

        // 🔹 Usuário autenticado com sucesso
        $user = Auth::user();

        // 🔹 Gera token Passport (JWT)
        $token = $user->createToken('api-token')->accessToken;

        return response()->json([
            'success' => true,
            'message' => 'Login realizado com sucesso!',
            'token'   => $token,
            'user'    => [
                'id'       => $user->id,
                'name'     => $user->name,
                'email'    => $user->email,
                'apelido'  => $user->apelido,
                'is_admin' => $user->is_admin,
            ],
        ], 200);
    }

    /**
     * Retorna o usuário autenticado
     */
    public function me(Request $request)
    {
        return response()->json($request->user());
    }

    /**
     * Logout - revoga token atual
     */
    public function logout(Request $request)
    {
        $token = $request->user()->token();
        $token->revoke();

        return response()->json([
            'success' => true,
            'message' => 'Logout realizado com sucesso.'
        ]);
    }
}
