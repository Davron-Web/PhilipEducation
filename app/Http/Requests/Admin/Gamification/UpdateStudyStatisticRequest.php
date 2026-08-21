<?php

namespace App\Http\Requests\Admin\Gamification;

use Illuminate\Foundation\Http\FormRequest;

class UpdateStudyStatisticRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'total_lessons_completed' => ['sometimes', 'integer', 'min:0'],
            'total_tests_passed' => ['sometimes', 'integer', 'min:0'],
            'total_words_learned' => ['sometimes', 'integer', 'min:0'],
            'study_time_minutes' => ['sometimes', 'integer', 'min:0'],
        ];
    }
}
