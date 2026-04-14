<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class AuthRequest extends FormRequest
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
            'email' => 'required|email|exists:usuarios,email',
            'password' => 'required'
        ];
    }

    public function messages()
    {
        return [
            'email.required' => 'O e-mail é obrigatório.',
            'email.email' => 'Insira um e-mail válido',
            'email.exists' => 'E-mail ou senha incorretos.',
            'password.required' => 'A senha é obrigatória.'
        ];
    }
}
