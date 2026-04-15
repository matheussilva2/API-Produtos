<?php

namespace Database\Seeders;

use App\Models\Pedido;
use App\Models\PedidoItem;
use App\Models\Produto;
use App\Models\Usuario;
use Illuminate\Database\Seeder;

class PedidoItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = Usuario::where('tipo', 'cliente')->get();
        $products = Produto::all();

        if($products->isEmpty()) {
            $products = Produto::factory()->count(10)->create();
        }

        if($users->isEmpty()) {
            $users = Usuario::factory()->count(10)->create();
        }

        foreach($users as $user) {
            for($i=0; $i < 2; $i++) {
                $order = Pedido::create([
                    'usuario_id' => $user->id,
                    'status' => 'CRIADO',
                    'logradouro_entrega' => 'Avenida Teste, ' . rand(1,100),
                    'cidade_entrega' => 'Maceió',
                    'estado_entrega' => 'AL',
                    'cep_entrega' => '57063240',
                    'total' => 0
                ]);

                $randomProducts = $products->random(rand(1, 3));
                $totalItemsPrice = 0;

                foreach($randomProducts as $product) {
                    $amount = rand(1, 3);
                    $subTotal = floatval($product->preco) * $amount;

                    $totalItemsPrice += $subTotal;

                    PedidoItem::create([
                        'pedido_id' => $order->id,
                        'produto_id' => $product->id,
                        'quantidade' => $amount,
                        'preco_unitario' => $product->preco,
                        'sub_total' => $subTotal
                    ]);
                }

                $order->update([
                    'total' => $totalItemsPrice
                ]);
            }
        }
    }
}
