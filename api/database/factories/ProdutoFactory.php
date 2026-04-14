<?php

namespace Database\Factories;

use App\Models\Produto;
use App\Models\Usuario;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Produto>
 */
class ProdutoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $user = Usuario::factory()->create(['tipo' => 'admin']);
        $nome = fake()->unique()->words(3, true);

        return [
            'nome' => ucfirst($nome),
            'url_imagem' => fake()->imageUrl(660, 500, 'technics'),
            'sku' => strtoupper(Str::limit(Str::slug($nome), 3, '')) . fake()->unique()->numerify('-####'),
            'preco' => fake()->randomFloat(2, 10, 5000),
            'estoque' => fake()->numberBetween(0, 100),
            'ativo' => true,
            'criado_por' => $user->id
        ];
    }

    public function inativo(): static {
        return $this->state(function(){
            return ([
                'ativo' => false
            ]);
        });
    }
}
