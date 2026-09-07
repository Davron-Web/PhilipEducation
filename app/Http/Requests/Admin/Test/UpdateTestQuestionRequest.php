<?php

namespace App\Http\Requests\Admin\Test;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTestQuestionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'test_id' => ['sometimes', 'integer', 'exists:tests,id'],
            'question' => ['sometimes', 'string'],
            'type' => ['nullable', 'string', 'in:single_choice,multiple_choice,text'],
            'topic' => ['nullable', 'string', 'max:80', 'regex:/^[a-z0-9-]+$/'],
            'explanation' => ['nullable', 'string', 'max:2000'],
            'points' => ['nullable', 'integer', 'min:1'],
        ];
    }
}
