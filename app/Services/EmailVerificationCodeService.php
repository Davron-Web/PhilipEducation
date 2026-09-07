<?php

namespace App\Services;

use App\Models\Auth\EmailVerificationCode;
use App\Models\User;

/**
 * Выдача и проверка шестизначных кодов подтверждения почты.
 */
class EmailVerificationCodeService
{
    /** Сколько живёт код. */
    public const LIFETIME_MINUTES = 15;

    /** Сколько попыток ввода даётся на один код. */
    public const MAX_ATTEMPTS = 5;

    /**
     * Выдаёт новый код, заменяя прежний.
     *
     * Возвращает сам код — единственное место, где он существует в открытом
     * виде; дальше он уходит в письмо и больше нигде не сохраняется.
     */
    public function issue(User $user): string
    {
        // random_int, а не rand/mt_rand: криптостойкий источник. Обычный
        // генератор предсказуем по нескольким предыдущим значениям, и коды
        // соседних регистраций можно было бы вычислить.
        $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        EmailVerificationCode::updateOrCreate(
            ['user_id' => $user->id],
            [
                'code_hash' => $this->hash($code),
                'attempts' => 0,
                'expires_at' => now()->addMinutes(self::LIFETIME_MINUTES),
            ]
        );

        return $code;
    }

    /**
     * Проверяет введённый код.
     *
     * @return array{ok: bool, reason: ?string, attemptsLeft: int}
     *         reason: missing | expired | mismatch | exhausted
     */
    public function verify(User $user, string $code): array
    {
        $record = EmailVerificationCode::where('user_id', $user->id)->first();

        if (! $record) {
            return $this->result(false, 'missing');
        }

        if ($record->isExpired()) {
            $record->delete();

            return $this->result(false, 'expired');
        }

        if (hash_equals($record->code_hash, $this->hash($code))) {
            $record->delete();

            return $this->result(true);
        }

        $record->increment('attempts');

        // Код сгорает после исчерпания попыток: шесть цифр — это миллион
        // вариантов, и без лимита их перебирают за вечер.
        if ($record->fresh()->attempts >= self::MAX_ATTEMPTS) {
            $record->delete();

            return $this->result(false, 'exhausted');
        }

        return $this->result(false, 'mismatch', self::MAX_ATTEMPTS - $record->fresh()->attempts);
    }

    /** Есть ли у пользователя действующий код. */
    public function activeCodeFor(User $user): ?EmailVerificationCode
    {
        $record = EmailVerificationCode::where('user_id', $user->id)->first();

        return $record && ! $record->isExpired() ? $record : null;
    }

    /**
     * sha256 без соли: код короткий и живёт минуты, солить его незачем —
     * а вот сравнение обязано быть постоянным по времени, иначе по задержке
     * ответа можно подбирать хеш посимвольно.
     */
    private function hash(string $code): string
    {
        return hash('sha256', trim($code));
    }

    /** @return array{ok: bool, reason: ?string, attemptsLeft: int} */
    private function result(bool $ok, ?string $reason = null, int $attemptsLeft = 0): array
    {
        return ['ok' => $ok, 'reason' => $reason, 'attemptsLeft' => $attemptsLeft];
    }
}
