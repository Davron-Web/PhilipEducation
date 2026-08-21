<?php

namespace App\Http\Requests\Admin\Exercise;

use Illuminate\Foundation\Http\FormRequest;

class StoreExerciseQuestionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'exercise_id' => ['required', 'integer', 'exists:exercises,id'],
            'question' => ['required', 'string'],
            'correct_answer' => ['required', 'string'],
        ];
    }
}
