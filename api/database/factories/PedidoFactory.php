<?php

namespace Database\Factories;

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
            
            'status' => fake()->randomElement(['CRIADO', 'PAGO', 'CANCELADO']),
            
            'logradouro_entrega' => fake()->streetAddress() . ', 22',
            'cidade_entrega' => fake()->city(),
            'estado_entrega' => fake()->stateAbbr(),

            'valor_frete' => fake()->randomFloat(2, 10, 50),
            'total' => fake()->randomFloat(2, 100, 1000)
        ];
    }
}
