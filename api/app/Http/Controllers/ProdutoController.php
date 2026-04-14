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
            //Verificando se é sqlite por causa dos testes
            $isSqlite = config('database.default') === 'sqlite';
            
            $isSqlite ?
            $query->where('nome', 'like', "%{$request->nome}%")
            :
            $query->whereFullText('nome', $request->nome);
        }

        if($request->filled('sku')) {
            $query->where('sku', $request->sku);
        }

        if($request->filled('ativo')) {
            $query->where('ativo', $request->ativo);
        }

        $produtos = $query->orderBy('estoque', 'desc')
            ->paginate($request->per_page?$request->per_page:10);

        return response()->json($produtos);
    }

    public function show(Produto $id) {
        return;
    }
}
