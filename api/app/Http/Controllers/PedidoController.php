<?php

namespace App\Http\Controllers;

use App\Http\Requests\PedidoIndexRequest;
use App\Models\Pedido;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

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

    public function customerOrders(PedidoIndexRequest $request) {
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

    public function show($id) {
        $order = Pedido::find($id);

        if(!$order)
            return response()->json(['message' => 'Pedido não encontrado.'], Response::HTTP_NOT_FOUND);

        return response()->json([
            'data' => $order
        ]);
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
}
