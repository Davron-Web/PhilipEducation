<?php

namespace App\Http\Requests\Admin\Test;

use Illuminate\Foundation\Http\FormRequest;

class StoreTestAnswerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'question_id' => ['required', 'integer', 'exists:test_questions,id'],
            'answer' => ['required', 'string', 'max:255'],
            'is_correct' => ['nullable', 'boolean'],
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('is_correct')) {
            $this->merge([
                'is_correct' => filter_var($this->is_correct, FILTER_VALIDATE_BOOLEAN),
            ]);
        }
    }
}
