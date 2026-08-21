<?php

namespace App\Http\Requests\Admin\Content;

use Illuminate\Foundation\Http\FormRequest;

class StoreLessonContentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'lesson_id' => ['required', 'integer', 'exists:lessons,id'],
            'type' => ['required', 'in:text,video,audio,image,exercise'],
            'title' => ['nullable', 'string', 'max:255'],
            'content' => ['nullable', 'string'],
            'file_url' => ['nullable', 'string', 'max:255'],
            'order_number' => ['nullable', 'integer', 'min:0'],
        ];
    }
}
