<?php

namespace App\Http\Requests\Admin\Vocabulary;

use Illuminate\Foundation\Http\FormRequest;

class StoreWordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'lesson_id' => ['required', 'integer', 'exists:lessons,id'],
            'word' => ['required', 'string', 'max:255'],
            'transcription' => ['nullable', 'string', 'max:255'],
            'example' => ['nullable', 'string'],
            'image' => ['nullable', 'string', 'max:255'],
            'difficulty' => ['required', 'integer', 'min:1', 'max:5'],
        ];
    }
}
