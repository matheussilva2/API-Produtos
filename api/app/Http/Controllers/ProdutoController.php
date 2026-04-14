<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProdutoIndexRequest;
use App\Models\Produto;
use Illuminate\Http\Request;

use function PHPUnit\Framework\isNumeric;

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

    public function show(string $identifier) {
        if(is_numeric($identifier)) {
            $product = Produto::find($identifier);
        } else {
            $product = Produto::where('sku', strtoupper($identifier))->first();
        }

        if(!$product)
            return response()->json([], 404);

        return response()->json($product);
    }
}
