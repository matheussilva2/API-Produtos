<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class PedidoStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'logradouro_entrega' => 'required|string',
            'cidade_entrega' => 'required|string',
            'estado_entrega' => 'required|string|size:2',
            'cep_entrega' => 'required|string|size:8',
            'itens' => 'required|array|min:1',
            'itens.*.produto_id' => 'required|exists:produtos,id',
            'itens.*.quantidade' => 'required|integer|min:1'
        ];
    }

    public function messages(): array
    {
        return [
            'logradouro_entrega.required' => 'O endereço de entrega é obrigatório.',
            'cidade_entrega.required' => 'A cidade para entrega é obrigatória.',
            'estado_entrega.required' => 'O estado para entrega é obrigatório.',
            'estado_entrega.size' => 'O estado para entrega deve ser uma sigla. Ex.: "AL"',
            'cep_entrega.required' => 'O CEP é obrigatório.',
            'cep_entrega.size' => 'O CEP deve ter 8 dígitos (somente números).',

            'itens.required' => 'Informe pelo menos um produto para criar o pedido.',
            'itens.*.produto_id.required' => 'O ID do produto é obrigatório.',
            'itens.*.produto_id.exists' => 'Um ou mais produtos não foram encontrados.',

            'itens.*.quantidade.required' => 'A quantidade do produto é obrigatória.',
        ];
    }
}
