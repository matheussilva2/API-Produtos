<?php

namespace App\Http\Requests;

use App\Models\Produto;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProdutoUpdateRequest extends FormRequest
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
        $identifier = $this->route('produto');
        $product = Produto::where('id', $identifier)->orWhere('sku', $identifier)->first();

        return [
            'nome' => 'sometimes|string|min:3|max:255',
            'sku' => [
                'sometimes',
                'string',
                'max:16',
                Rule::unique('produtos', 'sku')->ignore($product?->id)
            ],
            'preco' => 'sometimes|numeric|min:0',
            'estoque' => 'sometimes|numeric|min:0',
            'imagem' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'ativo' => 'nullable|boolean'
        ];
    }

    public function messages(): array {
        return [
            'nome.min' => 'O nome deve ter pelo menos 3 caracteres.',
            'nome.max' => 'O nome do produto deve ter até 255 caracteres.',

            'sku.unique' => 'Já existe um produto com esse SKU.',

            'preco.min' => 'O preço do produto precisa ser positivo.',

            'estoque.integer' => 'O estoque do produto precisa ser um número inteiro.',
            
            'imagem.max' => 'A imagem não pode ter mais do que 2MB',
            'imagem.mimes' => 'O formato da imagem deve ser JPG, JPEG, PNG ou WEBP.'
        ];
    }
}
