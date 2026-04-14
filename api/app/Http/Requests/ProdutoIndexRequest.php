<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ProdutoIndexRequest extends FormRequest
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
            'nome' => 'nullable|string|max:100',
            'sku' => 'nullable|string|max:16',
            'per_page' => 'nullable|integer|min:1|max:100',
            'page' => 'nullable|integer|min:1',
            'ativo' => 'nullable|boolean'
        ];
    }

    public function messages(): array {
        return [
            'nome.max' => 'O nome deve ter até 100 caracteres',
            'sku.max' => 'O SKU deve ter até 16 caracteres',

            'per_page.max' => 'O limite de itens por página é 100.',
            'per_page.integer' => 'A quantidade de itens por página deve ter um número inteiro.',
            
            'page.integer' => 'A página deve ser um número inteiro.',
            'page.min' => 'A página deve ser um número maior do que zero.',
            
            'ativo.boolean' => 'O filtro "ativo" deve ser boolean.'
        ];
    }
}
