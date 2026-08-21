<?php

namespace App\Http\Requests\Admin\Exercise;

use Illuminate\Foundation\Http\FormRequest;

class UpdateExerciseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'lesson_id' => ['required', 'integer', 'exists:lessons,id'],
            'title' => ['required', 'string', 'max:255'],
            'type' => ['required', 'string', 'in:fill_blank,matching,listening,speaking,translation'],
            'instructions' => ['required', 'string'],
        ];
    }
}
