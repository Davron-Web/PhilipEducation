<?php

namespace App\Http\Requests\Admin\Gamification;

use Illuminate\Foundation\Http\FormRequest;

class StoreStudyStatisticRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user_id' => ['required', 'integer', 'exists:users,id', 'unique:study_statistics,user_id'],
            'total_lessons_completed' => ['nullable', 'integer', 'min:0'],
            'total_tests_passed' => ['nullable', 'integer', 'min:0'],
            'total_words_learned' => ['nullable', 'integer', 'min:0'],
            'study_time_minutes' => ['nullable', 'integer', 'min:0'],
        ];
    }
}
