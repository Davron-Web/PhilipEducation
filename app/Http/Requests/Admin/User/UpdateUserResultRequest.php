<?php

namespace App\Http\Requests\Admin\User;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserResultRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'test_id' => ['required', 'integer', 'exists:tests,id'],
            'score' => ['required', 'integer', 'min:0'],
            'passed' => ['required', 'boolean'],
            'attempt_date' => ['required', 'date'],
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('passed')) {
            $this->merge([
                'passed' => filter_var($this->passed, FILTER_VALIDATE_BOOLEAN),
            ]);
        }
    }
}
