<?php

use App\Models\Pedido;
use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;

uses(RefreshDatabase::class);

test('Admin pode listar todos os pedidos com filtro.', function() {
    $admin = Usuario::factory()->create(['tipo' => 'admin']);

    Pedido::factory()->create(['status' => 'PAGO', 'cidade_entrega' => 'maceió', 'estado_entrega' => 'AL']); 
    Pedido::factory()->create(['status' => 'CRIADO', 'cidade_entrega' => 'são paulo', 'estado_entrega' => 'SP']); 
    Pedido::factory()->create(['status' => 'CANCELADO', 'cidade_entrega' => 'salvador', 'estado_entrega' => 'BA']);

    $this->actingAs($admin, 'sanctum')
        ->getJson('/api/admin/pedidos?status=PAGO&cidade_entrega=maceió')
        ->assertStatus(Response::HTTP_OK)
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.status', 'PAGO');
});

test('Admin pode pegar dados do pedido', function() {
    $admin = Usuario::factory()->create(['tipo' => 'admin']);
    $order = Pedido::factory()->create([
        'cidade_entrega' => 'maceió'
    ]);

    $response = $this->actingAs($admin)->getJson("/api/admin/pedidos/{$order->id}");
    $response->assertStatus(Response::HTTP_OK);
    $response->assertJsonPath('data.cidade_entrega', 'maceió');
});

test('Cliente não pode listar todos os pedidos', function() {
    $user = Usuario::factory()->create(['tipo' => 'cliente']);

    $this->actingAs($user, 'sanctum')
        ->getJson('/api/admin/pedidos')
        ->assertStatus(Response::HTTP_FORBIDDEN);
});