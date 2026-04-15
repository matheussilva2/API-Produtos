<?php

use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

test('Registro do usuário com sucesso.', function() {
    $response = $this->postJson('/api/auth/register', [
        'nome' => 'Registro Teste',
        'email' => 'registro@teste.com',
        'password' => 'senha123',
        'password_confirmation' => 'senha123',
        'cpf' => '11122233344'
    ]);

    $response->assertStatus(201)->assertJsonStructure(['data' => ['access_token', 'usuario']]);

    $this->assertDatabaseHas('usuarios', ['email' => 'registro@teste.com']);
});

test('Registro com e-mail duplicado rejeitado.', function() {
    Usuario::factory()->create(['email' => 'existente@teste.com']);

    $response = $this->postJson('/api/auth/register', [
        'nome' => 'Teste',
        'email' => 'existente@teste.com',
        'password' => 'senha123',
        'password_confirmation' => 'senha123',
        'cpf' => '12345678910'
    ]);

    $response->assertStatus(422)->assertJsonValidationErrors(['email']);
});

test('Registro com CPF duplicado rejeitado.', function() {
    Usuario::factory()->create(['cpf' => '12345678910']);

    $response = $this->postJson('/api/auth/register', [
        'nome' => 'Teste',
        'email' => 'teste@teste.com',
        'password' => 'senha123',
        'password_confirmation' => 'senha123',
        'cpf' => '12345678910'
    ]);

    $response->assertStatus(422)->assertJsonValidationErrors(['cpf']);
});

test('Login com sucesso e retorno do token', function() {
    Usuario::factory()->create([
        'email' => 'teste@teste.com',
        'password' => Hash::make('senha123')
    ]);

    $response = $this->postJson('/api/auth/login', [
        'email' => 'teste@teste.com',
        'password' => 'senha123'
    ]);

    $response->assertStatus(200)
    ->assertJsonStructure(['data' => ['access_token', 'token_type']]);
});

test('Falha ao logar com senha incorreta', function() {
    Usuario::factory()->create([
        'email' => 'teste@teste.com',
        'password' => Hash::make('senha_correta')
    ]);

    $response = $this->postJson('/api/auth/login', [
        'email' => 'teste@teste.com',
        'password' => 'senha_incorreta'
    ]);

    $response->assertStatus(401)
        ->assertJson(['message' => 'E-mail ou senha inválidos.']);
});

test('Retornar dados do usuário logado', function() {
    $user = Usuario::factory()->create();
    $token = Auth::login($user);

    $response = $this->withToken($token)->getJson('/api/auth/me');

    $response->assertStatus(200)->assertJsonPath('data.email', $user->email);
});

test('Invalidação do token quando fazer logout', function() {
    $user = Usuario::factory()->create();
    $token = Auth::login($user);

    $this->withToken($token)->postJson('/api/auth/logout')->assertStatus(200);
    Auth::forgetUser();
    $this->withToken($token)->getJson('/api/auth/me')->assertStatus(401);
});