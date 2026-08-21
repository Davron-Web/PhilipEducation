<?php

namespace App\Http\Requests\Admin\Content;

use Illuminate\Foundation\Http\FormRequest;

class StoreGrammarTopicRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        // форма может слать description вместо theory_content
        if (! $this->filled('theory_content') && $this->has('description')) {
            $this->merge(['theory_content' => $this->input('description')]);
        }

        // пустой селект уровня -> null (пройдёт required только если выбран)
        if ($this->has('level_id') && $this->input('level_id') === '') {
            $this->merge(['level_id' => null]);
        }
    }

    public function rules(): array
    {
        return [
            'title'          => ['required', 'string', 'max:255'],
            'level_id'       => ['required', 'integer', 'exists:levels,id'],
            'theory_content' => ['required', 'string'],
            'order_number'   => ['nullable', 'integer', 'min:1'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required'          => 'Укажите название правила.',
            'level_id.required'       => 'Выберите уровень.',
            'theory_content.required' => 'Заполните теорию правила.',
        ];
    }
}
