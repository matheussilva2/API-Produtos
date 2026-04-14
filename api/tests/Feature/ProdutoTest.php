<?php

use App\Models\Produto;
use Illuminate\Foundation\Testing\RefreshDatabase;

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