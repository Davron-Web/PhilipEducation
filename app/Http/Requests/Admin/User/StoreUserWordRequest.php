<?php

namespace App\Http\Requests\Admin\User;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserWordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'word_id' => ['required', 'integer', 'exists:words,id'],
            'learned' => ['nullable', 'boolean'],
            'correct_answers' => ['nullable', 'integer', 'min:0'],
            'wrong_answers' => ['nullable', 'integer', 'min:0'],
            'last_reviewed_at' => ['nullable', 'date'],
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('learned')) {
            $this->merge([
                'learned' => filter_var($this->learned, FILTER_VALIDATE_BOOLEAN),
            ]);
        }
    }
}
