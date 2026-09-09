<?php

namespace App\Http\Requests\Public\User;

use App\Models\User;
use App\Rules\NotDisposableEmail;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Правится всегда собственный профиль: контроллер берёт Auth::user(),
        // id из запроса не принимается, поэтому проверять нечего.
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                // Те же правила, что при регистрации: подтверждения почты
                // больше нет, и проверка домена — единственное, что не даёт
                // сменить адрес на выдуманный.
                'email:rfc,dns',
                'max:255',
                Rule::unique(User::class)->ignore($this->user()->id),
                new NotDisposableEmail,
            ],
            // 2 МБ и только растровые форматы: аватар показывается кружком
            // 40-64 px, большего разрешения от него не нужно.
            'avatar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'remove_avatar' => ['nullable', 'boolean'],
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'email.email' => 'Такой почтовый домен не существует. Проверьте адрес — возможно, опечатка в части после «@».',
            'email.unique' => 'Этот адрес уже занят другим аккаунтом.',
            'email.lowercase' => 'Введите адрес строчными буквами.',
            'avatar.image' => 'Загрузите изображение: JPG, PNG или WebP.',
            'avatar.max' => 'Файл больше 2 МБ — выберите фото поменьше.',
        ];
    }
}
