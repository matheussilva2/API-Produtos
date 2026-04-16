<?php

use App\Models\Produto;
use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

uses(RefreshDatabase::class);

test('Produtos com paginação padrão (10 por página)', function() {
    Produto::factory()->count(15)->create();
    
    $response = $this->getJson('/api/produtos');

    $response->assertStatus(200)
        ->assertJsonCount(10, 'data')
        ->assertJsonPath('total', 15)
        ->assertJsonStructure([
            'data' => [
                '*' => ['id', 'nome', 'sku', 'preco', 'estoque', 'ativo']
            ],
            'links'
        ]);
});

test('Filtrar produtos pelo nome', function() {
    Produto::factory()->create(['nome' => 'Cadeira Gamer Profissional']);
    Produto::factory()->create(['nome' => 'Mouse Sem Fio Multilaser']);

    $response = $this->getJson('/api/produtos?nome=Gamer');

    $response->assertStatus(200)
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.nome', 'Cadeira Gamer Profissional');
});

test('Filtrar produtos pelo SKU exato', function() {
    Produto::factory()->create(['sku' => 'ABC-12345']);
    Produto::factory()->create(['sku' => 'DEF-98754']);

    $response = $this->getJson('/api/produtos?sku=ABC-12345');

    $response->assertStatus(200)
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.sku', 'ABC-12345');
});

test('Filtrar produtos por ativo/inativo', function() {
    Produto::factory()->count(3)->create(['ativo' => true]);
    Produto::factory()->inativo()->create(['nome' => 'Produto Inativo']);

    $response = $this->getJson('/api/produtos?ativo=0');

    $response->assertStatus(200)
        ->assertJsonCount(1,  'data')
        ->assertJsonPath('data.0.nome', 'Produto Inativo');
});

test('Alteração de resultados por página', function() {
    Produto::factory()->count(10)->create();

    $response = $this->getJson('/api/produtos?per_page=2');

    $response->assertStatus(200)
        ->assertJsonCount(2, 'data')
        ->assertJsonPath('per_page', 2);
});

test('Parâmetro per_page inválido', function() {
    $response = $this->getJson('/api/produtos?per_page=invalido');

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['per_page']);
});

test('Retorna produto se procurar por ID ou SKU', function() {
    Produto::factory()->create([
        'id' => 10,
        'sku' => 'TESTE-1020'
    ]);

    $this->getJson('/api/produtos/10')->assertStatus(200);
    $this->getJson('/api/produtos/TESTE-1020')->assertStatus(200);
});

test('Retorna 404 se não houver produtos com ID fornecido', function() {
    Produto::factory()->create([
        'id' => 10
    ]);

    $this->getJson('/api/produtos/11')->assertStatus(404);
});

test('Retorna 404 se não houver produtos com SKU fornecido', function() {
    Produto::factory()->create([
        'sku' => 'TESTE-1020'
    ]);

    $this->getJson('/api/produtos/TESTE')->assertStatus(404);
});

test('Falha ao criar produto se o usuário não for admin', function() {
    $user = Usuario::factory()->create([
        'tipo' => 'cliente'
    ]);

    $token = Auth::login($user);

    $response = $this->withToken($token)->postJson('/api/produtos', []);

    $response->assertStatus(Response::HTTP_FORBIDDEN);
});

test('Criar produto com imagem via upload', function() {
    $admin = Usuario::factory()->create([
        'tipo' => 'admin'
    ]);

    $token = Auth::login($admin);

    Storage::fake('public');

    $image = UploadedFile::fake()->image('teste.jpg');

    $product_data = [
        'nome' => 'iPhone 15 Pro',
        'sku' => 'iph-15-pro',
        'preco' => 8999.90,
        'estoque' => 10,
        'imagem' => $image
    ];

    $response = $this->withToken($token)->postJson('/api/produtos', $product_data);

    $response->assertStatus(201)
        ->assertJsonPath('data.sku', 'IPH-15-PRO');
    
    $product = Produto::first();
    Storage::disk('public')->assertExists($product->url_imagem);
});

test('Falha ao criar produto com imagem inválida', function() {
    $admin = Usuario::factory()->create([
        'tipo' => 'admin'
    ]);

    $token = Auth::login($admin);

    Storage::fake('public');

    $image = UploadedFile::fake()->create('document.pdf', 1024);

    $product_data = [
        'nome' => 'iPhone 15 Pro',
        'sku' => 'iph-15-pro',
        'preco' => 8999.90,
        'estoque' => 10,
        'imagem' => $image
    ];

    $response = $this->withToken($token)->postJson('/api/produtos', $product_data);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['imagem']);
});

test('Falha criar produto com imagem muito pesada (> 2MB)', function() {
    $admin = Usuario::factory()->create([
        'tipo' => 'admin'
    ]);

    $token = Auth::login($admin);

    Storage::fake('public');

    $image = UploadedFile::fake()->create('imagem.png', 3072, 'image/png'); //3MB

    $product_data = [
        'nome' => 'iPhone 15 Pro',
        'sku' => 'iph-15-pro',
        'preco' => 8999.90,
        'estoque' => 10,
        'imagem' => $image
    ];

    $response = $this->withToken($token)->postJson('/api/produtos', $product_data);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['imagem']);
});

test('Falha ao atualizar produto se o usuário não for admin', function() {
    $user = Usuario::factory()->create([
        'tipo' => 'cliente'
    ]);

    $token = Auth::login($user);

    $response = $this->withToken($token)->putJson('/api/produtos/1', []);

    $response->assertStatus(Response::HTTP_FORBIDDEN);
});

test('Atualização de produto com sucesso', function() {
    Storage::fake('public');

    $admin = Usuario::factory()->create([
        'tipo' => 'admin'
    ]);
        
    $this->actingAs($admin, 'sanctum');
    $product = Produto::factory()->create([
        'criado_por' => $admin->id,
        'url_imagem' => 'products/antiga.jpg'
    ]);

    $new_image = UploadedFile::fake()->image('nova_imagem.jpg');
    $response = $this->withToken($admin)->putJson("/api/produtos/{$product->id}", [
        'nome' => 'Produto Atualizado',
        'imagem' => $new_image
    ]);

    $response->assertStatus(200);

    Storage::disk('public')->assertMissing('products/antiga.jpg');
    Storage::disk('public')->assertExists($product->refresh()->url_imagem);

    $response->assertJsonPath('data.nome', 'Produto Atualizado');
});

test('Desativação de produto com sucesso', function() {
    $admin = Usuario::factory()->create([
        'tipo' => 'admin'
    ]);
        
    $this->actingAs($admin, 'sanctum');
    $product = Produto::factory()->create([
        'criado_por' => $admin->id,
        'ativo' => '0'
    ]);

    $response = $this->withToken($admin)->putJson("/api/produtos/{$product->id}", [
        'ativo' => false
    ]);

    $response->assertStatus(200);

    $response->assertJsonPath('data.ativo', false);
});