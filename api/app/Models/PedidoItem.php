<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['pedido_id', 'produto_id', 'quantidade', 'preco_unitario', 'sub_total'])]

class PedidoItem extends Model
{
    protected $table = 'pedido_itens';

    public function pedido() {
        return $this->belongsTo(Pedido::class);
    }

    public function produto() {
        return $this->belongsTo(Produto::class);
    }
}
