<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
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
            'email' => 'required|email|max:255|unique:usuarios',
            'password' => 'required|min:6|confirmed',
            'cpf' => 'required|size:11|unique:usuarios',
            'phone' => 'nullable|string'
        ];
    }

    public function messages()
    {
        return [
            'nome.required' => 'O nome é obrigatório.',
            'nome.string' => 'O nome deve ser um campo de texto.',
            'nome.max' => 'O nome deve conter até 255 caracteres.',

            'email.required' => 'O e-mail é obrigatório.',
            'email.email' => 'Insira um e-mail válido.',
            'email.max' => 'O e-mail deve ter até 255 caracteres.',
            'email.unique' => 'O e-mail já está cadastrado.',

            'password.required' => 'A senha é obrigatória.',
            'password.confirmed' => 'A senha e confirmação de senha não são iguais.',
            'password.min' => 'A senha deve ter pelo menos 6 caracteres.',
            
            'cpf.required' => 'O CPF é obrigatório.',
            'cpf.size' => 'O CPF deve ter 11 dígitos (somente números).',
            'cpf.unique' => 'Esse CPF já está cadastrado.',

            'phone.string' => 'O telefone deve ser uma string.'
        ];
    }
}
