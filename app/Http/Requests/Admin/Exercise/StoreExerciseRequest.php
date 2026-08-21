<?php

namespace App\Http\Requests\Admin\Exercise;

use Illuminate\Foundation\Http\FormRequest;

class StoreExerciseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'lesson_id' => 'required|exists:lessons,id',
            'title' => 'required|string|max:255',
            'type' => 'required|string|in:quiz,test,matching,fill_in_blank',
            'description' => 'nullable|string',
            'instructions' => 'required|string', // ← добавить
            'order_number' => 'nullable|integer',
            'is_active' => 'boolean',
        ];
    }
}
