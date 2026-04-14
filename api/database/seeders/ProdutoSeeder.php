<?php

namespace Database\Seeders;

use App\Models\Produto;
use App\Models\Usuario;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProdutoSeeder extends Seeder
{

    public function run(): void
    {
        $usuario = Usuario::where('tipo', 'admin')->first();

        Produto::insert([
            [
                'nome' => 'Teclado Mecânico RGB',
                'sku' => 'TEC-MECH-001',
                'url_imagem' => 'https://picsum.photos/seed/TEC-MECH-001/800/800',
                'preco' => 250,
                'ativo' => true,
                'criado_por' => $usuario->id,
                'estoque' => 150
            ],
            [
                'nome' => 'Mouse Gamer 16000 DPI',
                'sku' => 'MSE-GAMER-002',
                'url_imagem' => 'https://picsum.photos/seed/MSE-GAMER-002/800/800',
                'preco' => 180.50,
                'ativo' => true,
                'criado_por' => $usuario->id,
                'estoque' => 6,
            ],
            [
                'nome' => 'Monitor 24" Full HD',
                'sku' => 'MON-24-003',
                'url_imagem' => 'https://picsum.photos/seed/MON-24-003/800/800',
                'preco' => 899.90,
                'ativo' => false,
                'criado_por' => $usuario->id,
                'estoque' => 0
            ],
        ]);
    }
}
