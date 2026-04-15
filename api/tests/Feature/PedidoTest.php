<?php

use App\Enums\OrderStatus;
use App\Models\Pedido;
use App\Models\Produto;
use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;

uses(RefreshDatabase::class);

test('Admin pode listar todos os pedidos com filtro.', function() {
    $admin = Usuario::factory()->create(['tipo' => 'admin']);

    Pedido::factory()->create(['status' => OrderStatus::PAGO, 'cidade_entrega' => 'maceió', 'estado_entrega' => 'AL']); 
    Pedido::factory()->create(['status' => OrderStatus::CRIADO, 'cidade_entrega' => 'são paulo', 'estado_entrega' => 'SP']); 
    Pedido::factory()->create(['status' => OrderStatus::CANCELADO, 'cidade_entrega' => 'salvador', 'estado_entrega' => 'BA']);

    $this->actingAs($admin, 'sanctum')
        ->getJson('/api/pedidos?status=PAGO&cidade_entrega=maceió')
        ->assertStatus(Response::HTTP_OK)
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.status', 'PAGO');
});

test('Admin pode pegar dados do pedido', function() {
    $admin = Usuario::factory()->create(['tipo' => 'admin']);
    $order = Pedido::factory()->create([
        'cidade_entrega' => 'maceió'
    ]);

    $response = $this->actingAs($admin)->getJson("/api/pedidos/{$order->id}");
    $response->assertStatus(Response::HTTP_OK);
    $response->assertJsonPath('data.cidade_entrega', 'maceió');
});

test('Cliente não pode listar todos os pedidos', function() {
    $user = Usuario::factory()->create(['tipo' => 'cliente']);

    $this->actingAs($user, 'sanctum')
        ->getJson('/api/pedidos')
        ->assertStatus(Response::HTTP_FORBIDDEN);
});

test('Falha ao atualizar status de pedido pago', function() {
    $admin = Usuario::factory()->create(['tipo' => 'admin']);
    $order = Pedido::factory()->create(['status' => OrderStatus::PAGO->value]);

    $response = $this->actingAs($admin, 'sanctum')
        ->putJson("/api/pedidos/{$order->id}/status", [
            'status' => 'CANCELADO'
        ]);

    $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
        ->assertJsonPath('message', "O status do pedido está como {$order->status} e não pode ser alterado.");

    $this->assertEquals(OrderStatus::PAGO->value, $order->fresh()->status);
});

test('Falha ao atualizar status de pedido cancelado', function() {
    $admin = Usuario::factory()->create(['tipo' => 'admin']);
    $order = Pedido::factory()->create(['status' => 'CANCELADO']);

    $response = $this->actingAs($admin, 'sanctum')
        ->putJson("/api/pedidos/{$order->id}/status", [
            'status' => 'PAGO'
        ]);

    $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
        ->assertJsonPath('message', "O status do pedido está como {$order->status} e não pode ser alterado.");

    $this->assertEquals(OrderStatus::CANCELADO->value, $order->fresh()->status);
});

test('Sucesso ao alterar status do produto de CRIADO para PAGO', function() {
    $admin = Usuario::factory()->create(['tipo' => 'admin']);
    $order = Pedido::factory()->create(['status' => OrderStatus::CRIADO]);

    $response = $this->actingAs($admin, 'sanctum')
        ->putJson("/api/pedidos/{$order->id}/status", [
            'status' => 'PAGO'
        ]);

    $response->assertStatus(Response::HTTP_OK)
        ->assertJsonPath('message', "Status do pedido atualizado com sucesso.");

    $this->assertEquals(OrderStatus::PAGO->value, $order->fresh()->status);
});

test('Devolução do estoque com sucesso quando o pedido for cancelado', function() {
    $product = Produto::factory()->create(['estoque' => 10]);
    $order = Pedido::factory()->create(['status' => OrderStatus::CRIADO]);

    $order->items()->create([
        'produto_id' => $product->id,
        'quantidade' => 3,
        'preco_unitario' => 50,
        'sub_total' => 150
    ]);

    $product->update(['estoque' => 7]);

    $order->cancel();

    expect($product->fresh()->estoque)->toBe(10);
    expect($order->fresh()->status)->toBe(OrderStatus::CANCELADO->value);
});

test('Exibir pedidos do cliente corretamente', function() {
    $customerA = Usuario::factory()->create();
    $customerB = Usuario::factory()->create();
    
    Pedido::factory()->count(3)->create(['usuario_id' => $customerA->id]);
    Pedido::factory()->count(2)->create(['usuario_id' => $customerB->id]);

    $response = $this->actingAs($customerA)
        ->getJson('/api/pedidos/cliente');

    $response->assertStatus(200)
        ->assertJsonCount(3, 'data');

    foreach($response->json('data') as $order) {
        $this->assertEquals($customerA->id, $order['usuario_id']);
    }
});

test('Sucesso ao cliente acessar pedido.', function() {
    $customer = Usuario::factory()->create(['tipo' => 'cliente']);
    $order = Pedido::factory()->create([
        'usuario_id' => $customer->id
    ]);

    $response = $this->actingAs($customer, 'sanctum')
        ->getJson("/api/pedidos/cliente/{$order->id}");

    $response->assertStatus(200);
});

test('Falha ao cliente acessar pedido de outro cliente.', function() {
    $customerA = Usuario::factory()->create(['id' => 1,'tipo' => 'cliente']);
    $customerB = Usuario::factory()->create(['id' => 2, 'tipo' => 'cliente']);
    $order = Pedido::factory()->create([
        'id' => 1,
        'usuario_id' => $customerA->id
    ]);

    $response = $this->actingAs($customerB, 'sanctum')
        ->getJson("/api/pedidos/cliente/{$order->id}");

    $response->assertStatus(401);
});