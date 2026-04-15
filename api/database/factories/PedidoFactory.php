<?php

namespace Database\Factories;

use App\Enums\OrderStatus;
use App\Models\Pedido;
use App\Models\Usuario;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Pedido>
 */
class PedidoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'usuario_id' => Usuario::factory()->create(),
            
            'status' => fake()->randomElement(OrderStatus::values()),
            
            'logradouro_entrega' => fake()->streetAddress() . ', 22',
            'cidade_entrega' => fake()->city(),
            'estado_entrega' => fake()->stateAbbr(),
            'cep_entrega' => '57063240',

            'total' => fake()->randomFloat(2, 100, 1000)
        ];
    }
}
