<?php

namespace App\Http\Controllers;

use App\Http\Requests\AuthRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\Usuario;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    public function login(AuthRequest $request) {
        $credentials = $request->only(['email', 'password']);
        try {
            if(!$token = JWTAuth::attempt($credentials)) {
                return response()->json([
                    'message' => 'E-mail ou senha inválidos.'
                ], 401);
            }

            return response()->json([
                'access_token' => $token,
                'token_type' => 'bearer'
            ]);
        } catch (JWTException $e) {
            return response()->json(['message' => 'Falha ao criar o token.'], 500);
        }
    }

    public function logout() {
        JWTAuth::invalidate(JWTAuth::getToken());

        return response()->json([
            'message' => 'Deslogado com sucesso'
        ]);
    }

    public function me() {
        $user = Auth::user();

        if(!$user) {
            return response()->json([
                'message' => 'Usuário não encontrado.'
            ], 404);
        }

        return response()->json($user);
    }

    public function register(RegisterRequest $request) {
        $user = Usuario::create([
            'nome' => $request->nome,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'cpf' => $request->cpf,
            'tipo' => 'cliente',
            'telefone' => $request->telefone
        ]);

        $token = Auth::login($user);

        return response()->json([
            'message' => 'Usuário registrado com sucesso!',
            'usuario' => $user,
            'access_token' => $token,
            'token_type' => 'bearer'
        ], 201);
    }
}
