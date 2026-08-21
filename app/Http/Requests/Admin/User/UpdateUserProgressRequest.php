<?php

namespace App\Http\Requests\Admin\User;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserProgressRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'lesson_id' => ['required', 'integer', 'exists:lessons,id'],
            'is_completed' => ['required', 'boolean'],
            'progress_percent' => ['required', 'integer', 'min:0', 'max:100'],
            'time_spent' => ['nullable', 'integer', 'min:0'],
            'last_position' => ['nullable', 'integer', 'min:0'],
            'completed_at' => ['nullable', 'date'],
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('is_completed')) {
            $this->merge([
                'is_completed' => filter_var($this->is_completed, FILTER_VALIDATE_BOOLEAN)
            ]);
        }
    }
}
