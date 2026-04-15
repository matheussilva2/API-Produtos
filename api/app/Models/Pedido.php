<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['usuario_id', 'status', 'logradouro_entrega', 'cidade_entrega', 'estado_entrega', 'cep_entrega', 'valor_frete', 'total'])]
class Pedido extends Model
{
    use HasFactory;

    public function items()  {
        return $this->hasMany(PedidoItem::class, 'pedido_id');
    }

    protected function casts(): array {
        return [
            'valor_frete' => 'decimal:2',
            'total' => 'decimal:2'
        ];
    }
}
