<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['nome', 'url_imagem', 'sku', 'preco', 'ativo', 'criado_por', 'estoque'])]

class Produto extends Model
{
    use HasFactory;

    protected function casts(): array {
        return [
            'preco' => 'decimal:2',
            'ativo' => 'boolean'
        ];
    }
}
