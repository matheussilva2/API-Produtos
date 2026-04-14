<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProdutoIndexRequest;
use App\Models\Produto;
use Illuminate\Http\Request;

class ProdutoController extends Controller
{
    public function index(ProdutoIndexRequest $request) {
        $query = Produto::query();

        if($request->filled('nome')) {
            $query->whereFullText('nome', $request->nome);
        }

        if($request->filled('sku')) {
            $query->where('sku', $request->sku);
        }

        $produtos = $query->orderBy('estoque', 'desc')
            ->paginate($request->per_page || 10);

        return response()->json($produtos);
    }

    public function show(Produto $id) {
        return;
    }
}
