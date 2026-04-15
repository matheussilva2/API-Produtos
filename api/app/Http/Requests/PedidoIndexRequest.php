<?php

namespace App\Http\Requests;

use App\Enums\OrderStatus;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PedidoIndexRequest extends FormRequest
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
            'status' => ['nullable', Rule::in(OrderStatus::values()),],
            'cidade_entrega' => 'nullable|string',
            'estado_entrega' => 'nullable|string',
            'per_page' => 'nullable|integer|min:1|max:100',
        ];
    }

    public function messages(): array {
        return [
            'status.in' => 'O status do pedido precisa ser CRIADO, PAGO ou CANCELADO',
            'per_page.min' => 'A limite de itens por página precisa ser maior do que zero',
            'per_page.max' => 'O limite de itens por página é 100.',
            'per_page.integer' => 'A quantidade de itens por página deve ter um número inteiro.',
        ];
    }
}
