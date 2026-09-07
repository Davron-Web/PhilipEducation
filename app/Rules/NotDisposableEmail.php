<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Str;

/**
 * Отклоняет адреса на доменах одноразовой почты.
 *
 * Список живёт в config/disposable_email.php, чтобы пополнять его без
 * правки кода. Совпадение проверяется и по самому домену, и по его
 * поддоменам: сервисы раздают адреса вида user@team.mailinator.com.
 */
class NotDisposableEmail implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value) || ! str_contains($value, '@')) {
            // Формат проверяют правила email — здесь молчим, чтобы под полем
            // не появлялись два сообщения об одной и той же ошибке.
            return;
        }

        $domain = Str::lower(Str::afterLast($value, '@'));

        foreach ((array) config('disposable_email.blocked_domains', []) as $blocked) {
            $blocked = Str::lower($blocked);

            if ($domain === $blocked || str_ends_with($domain, '.'.$blocked)) {
                $fail('Одноразовые почтовые ящики не подойдут — на такой адрес не получится восстановить пароль. Укажите постоянную почту.');

                return;
            }
        }
    }
}
