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
            // Только латиница (плюс пробелы/дефис/апостроф для имён вида
            // "O'Brien-Smith") — поле специально для сертификата, кириллица
            // в нём саму задачу не решает.
            'name_latin' => ['nullable', 'string', 'max:255', 'regex:/^[a-zA-Z\'\-\s]+$/'],
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
            // Аватар — отдельная форма (UpdateUserAvatarRequest, галерея
            // готовых картинок), не часть этой формы: загрузки файла для
            // аватара в профиле больше нет.
            'show_in_leaderboard' => ['nullable', 'boolean'],
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'email.email' => 'Такой почтовый домен не существует. Проверьте адрес — возможно, опечатка в части после «@».',
            'email.unique' => 'Этот адрес уже занят другим аккаунтом.',
            'email.lowercase' => 'Введите адрес строчными буквами.',
            'name_latin.regex' => 'Только латинские буквы (без цифр и кириллицы).',
        ];
    }
}
