<?php

namespace App\Http\Requests\Admin\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $userId = $this->route('user')?->id;

        return [
            'name' => ['sometimes', 'string', 'max:255'],
            'email' => ['sometimes', 'email', 'max:255', 'unique:users,email,' . $userId],
            'password' => ['nullable', Password::defaults()],
            'role_id' => ['nullable', 'integer', 'exists:roles,id'],
            'level_id' => ['nullable', 'integer', 'exists:levels,id'],
        ];
    }
}
