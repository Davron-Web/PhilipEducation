<?php

namespace App\Http\Requests\Admin\Book;

use Illuminate\Foundation\Http\FormRequest;

class StoreBookRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('level_id') && $this->input('level_id') === '') {
            $this->merge(['level_id' => null]);
        }
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'author' => ['required', 'string', 'max:255'],
            'level_id' => ['nullable', 'integer', 'exists:levels,id'],
            'description' => ['nullable', 'string'],
            'cover_image' => ['nullable', 'string', 'max:500'],
            'is_published' => ['nullable', 'boolean'],
            'pages' => ['nullable', 'array'],
            'pages.*.title' => ['nullable', 'string', 'max:255'],
            'pages.*.content' => ['required_with:pages', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Укажите название книги.',
            'author.required' => 'Укажите автора.',
            'pages.*.content.required_with' => 'Текст страницы не может быть пустым.',
        ];
    }
}
