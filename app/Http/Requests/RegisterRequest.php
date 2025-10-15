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
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:society,owner',
        ];

        if ($this->role === 'owner') {
            $rules['kos_name'] = 'required|string|max:255';
            $rules['address'] = 'required|string';
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'role.in' => 'Role must be either society or owner.',
            'kos_name.required' => 'Kos name is required for owner role.',
            'address.required' => 'Address is required for owner role.',
        ];
    }
}