<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class KnowledgeSuggestionApproveRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && in_array(auth()->user()->role, ['Superadmin', 'Admin'], true);
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:180'],
            'answer' => ['required', 'string'],
            'source' => ['required', 'string', 'max:255'],
        ];
    }
}
