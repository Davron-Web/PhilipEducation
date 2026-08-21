<?php

namespace App\Http\Requests\Admin\System;

use Illuminate\Foundation\Http\FormRequest;

class UpdateLevelRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $levelId = $this->route('level')?->id;

        return [
            'code' => ['sometimes', 'string', 'max:10', 'unique:levels,code,' . $levelId, 'in:A1,A2,B1,B2,C1,C2'],
            'name' => ['sometimes', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ];
    }
}
