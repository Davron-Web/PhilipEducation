<?php

namespace App\Http\Requests\Admin\Content;

use Illuminate\Foundation\Http\FormRequest;

class StoreLessonRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    // Пустое значение из селекта превращаем в null
    protected function prepareForValidation(): void
    {
        if ($this->has('level_id') && $this->input('level_id') === '') {
            $this->merge(['level_id' => null]);
        }
    }

    public function rules(): array
    {
        return [
            'title'             => ['required', 'string', 'max:255'],
            'description'       => ['nullable', 'string'],
            'level_id'          => ['nullable', 'integer', 'exists:levels,id'],
            'order_number'      => ['nullable', 'integer', 'min:1'],
            'estimated_minutes' => ['nullable', 'integer', 'min:1'],
            'is_published'      => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Укажите название урока.',
        ];
    }
}
