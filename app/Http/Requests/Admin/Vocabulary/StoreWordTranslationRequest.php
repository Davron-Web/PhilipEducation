<?php

namespace App\Http\Requests\Admin\Vocabulary;

use Illuminate\Foundation\Http\FormRequest;

class StoreWordTranslationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'word_id' => ['required', 'integer', 'exists:words,id'],
            'language' => ['required', 'string', 'max:10', 'in:ru,kz,en'],
            'translation' => ['required', 'string', 'max:255'],
            'definition' => ['nullable', 'string'],
            'example' => ['nullable', 'string'],
        ];
    }
}
