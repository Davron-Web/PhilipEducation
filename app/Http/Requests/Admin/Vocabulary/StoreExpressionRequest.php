<?php

namespace App\Http\Requests\Admin\Vocabulary;

use Illuminate\Foundation\Http\FormRequest;

class StoreExpressionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'text' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:idiom,phrasal_verb,proverb,collocation'],
            'transcription' => ['nullable', 'string', 'max:255'],
            'example' => ['nullable', 'string'],
            'meaning' => ['nullable', 'string'],
            'literal_translation' => ['nullable', 'string', 'max:255'],
            'difficulty' => ['required', 'integer', 'min:1', 'max:5'],
            'category' => ['nullable', 'string', 'max:255'],
            'level_id' => ['nullable', 'integer', 'exists:levels,id'],
            'base_verb' => ['nullable', 'string', 'max:255'],
            'particle' => ['nullable', 'string', 'max:255'],
            'separable' => ['nullable', 'boolean'],
        ];
    }
}
