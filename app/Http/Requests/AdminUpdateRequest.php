<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AdminUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->role === 'Superadmin';
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', Rule::unique('users', 'username')->ignore($this->route('admin'))],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($this->route('admin'))],
            'password' => ['nullable', 'string', 'min:8'],
            'role' => ['required', Rule::in(['Superadmin', 'Admin'])],
            'verified' => ['nullable', 'boolean'],
        ];
    }
}
