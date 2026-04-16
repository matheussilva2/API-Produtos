<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ProdutoStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nome' => 'required|string|max:255',
            'imagem' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
            'sku' => 'required|string|max:16|unique:produtos,sku',
            'estoque' => 'required|numeric|min:0',
            'preco' => 'required|numeric|min:0',
            'ativo' => 'nullable|boolean'
        ];
    }

    public function messages(): array {
        return [
            'nome.required' => 'O nome do produto é obrigatório.',
            'nome.min' => 'O nome deve ter pelo menos 3 caracteres.',
            'nome.max' => 'O nome não pode ter mais do que 255 caracteres.',

            'imagem.required' => 'A imagem do produto é obrigatória',
            'imagem.max' => 'A imagem não pode ter mais do que 2MB',
            'imagem.mimes' => 'O formato da imagem deve ser JPG, JPEG, PNG ou WEBP.',

            'sku.required' => 'O SKU é obrigatório',
            'sku.max' => 'O SKU não pode ser maior do que 16 caracteres.',
            'sku.unique' => 'Já existe um produto com esse SKU cadastrado.',

            'estoque.required' => 'Forneça a quantidade do produto em estoque.',
            'estoque.integer' => 'O estoque do produto precisa ser um número inteiro.',
            'estoque.min' => 'A quantidade em estoque não pode ser menor do que zero.',

            'preco.required' => 'O preço do produto é obrigatório',
            'preco.numeric' => 'O preço do produto tem que ser um número.',
            'preco.min' => 'O preço do produto não pode ser menor do que zero.',

            'ativo.boolean' => 'O campo "ativo" deve ser boolean (1 ou 0)'
        ];
    }
}
