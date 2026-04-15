<?php

namespace App\Models;

use App\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

#[Fillable(['usuario_id', 'status', 'logradouro_entrega', 'cidade_entrega', 'estado_entrega', 'cep_entrega', 'total'])]
class Pedido extends Model
{
    use HasFactory;

    public function items()  {
        return $this->hasMany(PedidoItem::class, 'pedido_id');
    }

    public function changeStatus(OrderStatus $new_status) {
        switch($new_status) {
            case OrderStatus::CRIADO:
                $this->update(['status' => OrderStatus::CRIADO]);
                break;
            case OrderStatus::PAGO:
                $this->update(['status' => OrderStatus::PAGO]);
                break;
            case OrderStatus::CANCELADO:
                $this->cancel();
        }
    }

    public function cancel() {
        if($this->status !== OrderStatus::CRIADO)
            return false;

        return DB::transaction(function() {
            foreach($this->items as $item) {
                $item->produto()->increment('estoque', $item->quantidade);
            }

            return $this->update(['status' => OrderStatus::CANCELADO]);
        });

        $this->update(['status' => OrderStatus::CANCELADO]);
    }

    protected function casts(): array {
        return [
            'total' => 'decimal:2'
        ];
    }
}
