<?php

namespace App\Http\Requests\Admin\Gamification;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTitleRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Раздел админки уже закрыт middleware role:admin.
        return true;
    }

    public function rules(): array
    {
        return [
            'code' => ['required', 'string', 'max:60', 'unique:titles,code,'.$this->route('title')->id],
            'name' => ['required', 'string', 'max:120'],
            'description' => ['nullable', 'string', 'max:255'],
            'icon' => ['nullable', 'string', 'max:16'],
            'min_xp' => ['required', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge(['is_active' => $this->boolean('is_active')]);
    }
}
