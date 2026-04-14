<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Usuario;
use Illuminate\Support\Facades\Hash;

class UsuarioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Usuario::insert(
            [
                [
                    'nome' => 'Admin',
                    'email' => 'admin@teste.com',
                    'password' => Hash::make('senha123'),
                    'cpf' => '12345678901',
                    'tipo' => 'admin',
                    'telefone' => null
                ],
                [
                    'nome' => 'Cliente',
                    'email' => 'cliente@teste.com',
                    'password' => Hash::make('senha123'),
                    'cpf' => '98765432101',
                    'tipo' => 'cliente',
                    'telefone' => '5599888888888'
                ]
            ]
        );
        
    }
}
