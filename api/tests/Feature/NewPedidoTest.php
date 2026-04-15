<?php

use App\Models\Produto;
use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;

uses(RefreshDatabase::class);

test('Realizar pedido com sucesso e reduzir estoque', function() {
    $user = Usuario::factory()->create(['tipo' => 'cliente']);
    $product = Produto::factory()->create([
        'nome' => 'Teclado Mecânico',
        'preco' => 100,
        'estoque' => 10
    ]);

    $payload = [
        'logradouro_entrega' => 'Avenida Teste, 15',
        'cidade_entrega' => 'Maceió',
        'estado_entrega' => 'AL',
        'cep_entrega' => '57063240',
        'itens' => [
            [
                'produto_id' => $product->id,
                'quantidade' => 2
            ]
        ]
    ];

    $response = $this->actingAs($user, 'sanctum')->postJson('/api/pedidos', $payload);
    $response->assertStatus(Response::HTTP_CREATED);
    $response->assertJsonPath('message', 'Pedido realizado com sucesso!');

    $this->assertDatabaseHas('pedidos', ['usuario_id' => $user->id, 'total' => 215]);
    $this->assertDatabaseHas('pedido_itens', [
        'produto_id' => $product->id,
        'quantidade' => 2,
        'preco_unitario' => 100
    ]);

    $this->assertEquals(8, $product->fresh()->estoque);
});

test('Rollback aconteceu quando estoque foi insuficiente', function() {
    $user = Usuario::factory()->create();
    $product = Produto::factory()->create(['estoque' => 1, 'preco' => 50]);

    $payload = [
        'logradouro_entrega' => 'Avenida Teste',
        'cidade_entrega' => 'Maceió',
        'estado_entrega' => 'AL',
        'cep_entrega' => '57063240',
        'itens' => [
            ['produto_id' => $product->id, 'quantidade' => 5]
        ]
    ];

    $response = $this->actingAs($user, 'sanctum')
        ->postJson('/api/pedidos', $payload);
    
    $response->assertStatus(Response::HTTP_INTERNAL_SERVER_ERROR);

    $this->assertDatabaseCount('pedidos', 0);
    $this->assertDatabaseCount('pedido_itens', 0);

    $this->assertEquals(1, $product->fresh()->estoque);
});