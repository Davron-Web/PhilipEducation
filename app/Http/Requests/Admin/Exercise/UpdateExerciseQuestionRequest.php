<?php

namespace App\Http\Requests\Admin\Exercise;

use Illuminate\Foundation\Http\FormRequest;

class UpdateExerciseQuestionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'exercise_id' => ['sometimes', 'integer', 'exists:exercises,id'],
            'question' => ['sometimes', 'string'],
            'correct_answer' => ['sometimes', 'string'],
        ];
    }
}
