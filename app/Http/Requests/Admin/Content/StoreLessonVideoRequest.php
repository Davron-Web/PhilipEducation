<?php

namespace App\Http\Requests\Admin\Content;

use Illuminate\Foundation\Http\FormRequest;

class StoreLessonVideoRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Раздел закрыт middleware role:admin.
        return true;
    }

    public function rules(): array
    {
        return [
            'lesson_id' => ['required', 'exists:lessons,id'],
            'title' => ['required', 'string', 'max:200'],
            // Только http(s): иначе в src плеера можно было бы положить
            // javascript: и получить исполнение чужого кода на странице.
            'url' => ['required', 'url:http,https', 'max:500'],
            'description' => ['nullable', 'string', 'max:255'],
            'duration_minutes' => ['nullable', 'integer', 'min:1', 'max:600'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:999'],
            'is_published' => ['nullable', 'boolean'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_published' => $this->boolean('is_published'),
            'sort_order' => $this->input('sort_order', 0),
        ]);
    }
}
