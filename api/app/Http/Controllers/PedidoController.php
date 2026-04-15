<?php

namespace App\Http\Controllers;

use App\Enums\OrderStatus;
use App\Http\Requests\PedidoIndexRequest;
use App\Http\Requests\PedidoStoreRequest;
use App\Http\Requests\PedidoUpdateStatusRequest;
use App\Models\Pedido;
use App\Models\PedidoItem;
use App\Models\Produto;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PedidoController extends Controller
{
    public function index(PedidoIndexRequest $request) {
        $query = Pedido::query();

        if($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if($request->filled('cidade_entrega')) {
            $query->where('cidade_entrega', $request->cidade_entrega);
        }

        if($request->filled('estado_entrega')) {
            $query->where('estado_entrega', $request->estado_entrega);
        }

        $orders = $query->orderBy('created_at', 'desc')->paginate($request->per_page?$request->per_page:10);

        return response()->json($orders);
    }

    public function show($id) {
        $order = Pedido::find($id);

        if(!$order)
            return response()->json(['message' => 'Pedido não encontrado.'], Response::HTTP_NOT_FOUND);

        return response()->json([
            'data' => $order
        ]);
    }

    public function indexCustomerOrders(PedidoIndexRequest $request) {
        $user = Auth::user();

        $orders = Pedido::where('usuario_id', $user->id);

        if($request->filled('status')) {
            $orders->where('status', $request->status);
        }

        if($request->filled('cidade_entrega')) {
            $orders->where('cidade_entrega', $request->cidade_entrega);
        }

        if($request->filled('estado_entrega')) {
            $orders->where('estado_entrega', $request->estado_entrega);
        }

        $orders->orderBy('created_at', 'desc')->paginate($request->per_page?$request->per_page:10);

        return response()->json($orders);
    }

    public function showCustomerOrder(Pedido $order) {
        $user = Auth::user();

        if($user->id !== $order->id) {
            return response()->json([
                'message' => 'Sem autorização.'
            ], Response::HTTP_FORBIDDEN);
        }

        return response()->json([
            'data' => $order
        ]);
    }

    public function store(PedidoStoreRequest $request) {
        return DB::transaction(function () use($request){
            $user = Auth::user();
            $totalOrder = 0;

            $order = Pedido::create([
                'usuario_id' => $user->id,
                'status' => OrderStatus::CRIADO,
                'logradouro_entrega' => $request->logradouro_entrega,
                'cidade_entrega' => $request->cidade_entrega,
                'estado_entrega' => $request->estado_entrega,
                'cep_entrega' => $request->cep_entrega,
                'total' => 0
            ]);

            foreach($request->itens as $item) {
                $product = Produto::lockForUpdate()->find($item['produto_id']);

                if($product->estoque < $item['quantidade']) {
                    throw new \Exception("Estoque insuficiente para o produto [$product->sku] {$product->nome}");
                }

                $subTotal = $product->preco * $item['quantidade'];
                $totalOrder = $subTotal;

                PedidoItem::create([
                    'pedido_id' => $order->id,
                    'produto_id' => $product->id,
                    'quantidade' => $item['quantidade'],
                    'preco_unitario' => $product->preco,
                    'sub_total' => $subTotal
                ]);

                $product->decrement('estoque', $item['quantidade']);
            }

            $order->update([
                'total' => $totalOrder
            ]);

            return response()->json([
                'message' => 'Pedido realizado com sucesso!',
                'data' => $order->load('items.produto')
            ], Response::HTTP_CREATED);
        });
    }

    public function update(PedidoUpdateStatusRequest $request, string $id) {
        $order = Pedido::find($id);

        if(!$order)
            return response()->json([
                'message' => 'Pedido não encontrado.'
            ], Response::HTTP_NOT_FOUND);
        
        if(in_array($order->status, [OrderStatus::PAGO->value, OrderStatus::CANCELADO->value])) {
            return response()->json([
                'message' => "O status do pedido está como {$order->status} e não pode ser alterado."
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $statusEnum = OrderStatus::from($request->status);

        $order->changeStatus($statusEnum);

        return response()->json([
            'message' => 'Status do pedido atualizado com sucesso.',
            'data' => $order
        ]);
    }
}
