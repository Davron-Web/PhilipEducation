<?php

namespace App\Http\Requests\Public\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserAvatarRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Правится всегда собственный аватар: контроллер берёт Auth::user(),
        // id из запроса не принимается.
        return true;
    }

    public function rules(): array
    {
        return [
            // Только имя файла из готового набора — не произвольная строка:
            // иначе через этот же путь можно было бы записать в users.avatar
            // что угодно, вплоть до внешнего URL или ../-пути.
            'avatar' => ['required', 'string', Rule::in(config('avatars.files', []))],
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'avatar.required' => 'Выберите аватар.',
            'avatar.in' => 'Такого аватара нет в наборе.',
        ];
    }
}
