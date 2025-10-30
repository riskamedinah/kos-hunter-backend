<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:society,owner',
            'phone' => 'required|string|max:15|unique:users',
        ];
    }

    public function messages(): array
    {
        return [
            'role.in' => 'Role must be either society or owner.',
            'phone.required' => 'Phone number is required.',
            'phone.unique' => 'Phone number already registered.',
        ];
    }
}