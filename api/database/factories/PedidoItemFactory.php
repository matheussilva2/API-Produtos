<?php

namespace Database\Factories;

use App\Models\Pedido;
use App\Models\PedidoItem;
use App\Models\Produto;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PedidoItem>
 */
class PedidoItemFactory extends Factory
{
    
    public function definition(): array
    {
        $amount = fake()->numberBetween(1, 5);
        $unit_price = fake()->randomFloat(2, 10, 500);

        return [
            'pedido_id' => Pedido::factory()->create(),
            'produto_id' => Produto::factory()->create(),
            'quantidade' => $amount,
            'preco_unitario' => $unit_price,
            'subtotal' => $unit_price * $amount
        ];
    }
}
