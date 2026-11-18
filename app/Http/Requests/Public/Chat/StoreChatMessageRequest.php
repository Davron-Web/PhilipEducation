<?php

namespace App\Http\Requests\Public\Chat;

use App\Services\ChatModerationService;
use Illuminate\Contracts\Validation\Validator as ValidatorContract;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Validator;

class StoreChatMessageRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Отправлять может любой авторизованный пользователь (кроме
        // забаненных — это отдельная проверка в контроллере, не здесь:
        // здесь мы валидируем ФОРМУ сообщения, а не право писать вообще).
        return true;
    }

    public function rules(): array
    {
        return [
            'body' => [
                'required',
                'string',
                'max:500',
                // required не ловит строку из одних пробелов — она technically
                // непустая до trim().
                function (string $attribute, mixed $value, \Closure $fail) {
                    if (trim((string) $value) === '') {
                        $fail('Сообщение не может состоять из одних пробелов.');
                    }
                },
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'body.required' => 'Введите текст сообщения.',
            'body.max' => 'Сообщение длиннее 500 символов.',
        ];
    }

    /**
     * Мат и ссылки проверяются здесь, а не в контроллере — так контроллер
     * остаётся тонким, а правила отправки сообщения (включая "что нельзя
     * писать", а не только "какой длины") живут одним файлом.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $body = (string) $this->input('body');

            if ($body === '' || $validator->errors()->has('body')) {
                return;
            }

            $moderation = app(ChatModerationService::class);

            if ($moderation->containsProfanity($body)) {
                $validator->errors()->add('body', 'Сообщение содержит недопустимую лексику.');

                return;
            }

            // Ссылки разрешены только админам — иначе чат мгновенно
            // зарастёт спамом.
            if (! $this->user()->isAdmin() && $moderation->containsLink($body)) {
                $validator->errors()->add('body', 'Ссылки в чате запрещены.');
            }
        });
    }

    /**
     * По умолчанию FormRequest на непустую ошибку валидации делает
     * redirect()->back() с ошибками во flash-сессии — это поведение для
     * обычных Blade-форм. У этого приложения ответ в JSON решает не
     * Accept-заголовок запроса, а путь (bootstrap/app.php:
     * shouldRenderJsonWhen — JSON только под /api/*), а чат живёт под
     * /chat, не /api — без этого переопределения ошибка валидации
     * отправляла бы fetch() чата на 302 на предыдущую страницу вместо
     * 422 с телом ошибок, и JS не смог бы их показать.
     */
    protected function failedValidation(ValidatorContract $validator): void
    {
        throw new HttpResponseException(
            response()->json([
                'message' => 'Проверьте сообщение и попробуйте снова.',
                'errors' => $validator->errors(),
            ], 422)
        );
    }
}
