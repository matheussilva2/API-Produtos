<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProdutoIndexRequest;
use App\Http\Requests\ProdutoStoreRequest;
use App\Http\Requests\ProdutoUpdateRequest;
use App\Models\Produto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

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
        $product = Produto::where('id', $identifier)->orWhere('sku', $identifier)->first();

        if(!$product)
            return response()->json([], 404);

        return response()->json($product);
    }

    public function store(ProdutoStoreRequest $request) {
        $data = $request->validated();

        $data['sku'] = strtoupper($data['sku']);

        if($request->hasFile('imagem')) {
            $path = $request->file('imagem')->store('products', 'public');
            $data['url_imagem'] = $path;
        }

        $data['criado_por'] = Auth::id();

        $product = Produto::create($data);

        return response()->json([
            'message' => 'Produto criado com sucesso.',
            'data' => $product
        ], 201);
    }

    public function update(ProdutoUpdateRequest $request, string $identifier) {
        $product = Produto::where('id', $identifier)
            ->orWhere('sku', strtoupper($identifier))
            ->first();
        
        if(!$product) {
            return response()->json(['message' => 'Produto não encocntrado'], 404);
        }

        $data = $request->validated();

        if(isset($data['sku']))
            $data['sku'] = strtoupper($data['sku']);

        if($request->hasFile('imagem')) {
            if($product->url_imagem) {
                Storage::disk('public')->delete($product->url_imagem);
            }

            $path = $request->file('imagem')->store('products', 'public');
            $data['url_imagem'] = $path;
        }

        $product->update($data);

        return response()->json([
            'message' => 'Produto atualizado com sucesso.',
            'data' => $product
        ]);
    }
}
