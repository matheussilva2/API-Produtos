<?php

namespace App\Http\Controllers;

use App\Http\Requests\AuthRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\Usuario;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    /**
    * Fazer login e receber token
    * 
    */
    public function login(AuthRequest $request) {
        $credentials = $request->only(['email', 'password']);
        try {
            if(!$token = JWTAuth::attempt($credentials)) {
                return response()->json([
                    'message' => 'E-mail ou senha inválidos.'
                ], Response::HTTP_UNAUTHORIZED);
            }

            return response()->json([
                'data' => [
                    'access_token' => $token,
                    'token_type' => 'bearer'
                ]
            ]);
        } catch (JWTException $e) {
            return response()->json(['message' => 'Falha ao criar o token.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
    * Deslogar conta e invalidar token
    * 
    */
    public function logout() {
        JWTAuth::invalidate(JWTAuth::getToken());

        return response()->json([
            'message' => 'Deslogado com sucesso'
        ]);
    }

    /**
    * Dados do usuário logado
    * 
    * @response array{ data: \App\Models\Usuario}
    */
    public function me() {
        $user = Auth::user();

        if(!$user) {
            return response()->json([
                'message' => 'Usuário não encontrado.'
            ], Response::HTTP_NOT_FOUND);
        }

        return response()->json([
            'data' => $user
        ]);
    }

    /**
    * Registrar novo usuário
    * 
    */
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
            'data' => [
                'usuario' => $user,
                'access_token' => $token,
                'token_type' => 'bearer'
            ]
        ], Response::HTTP_CREATED);
    }
}
