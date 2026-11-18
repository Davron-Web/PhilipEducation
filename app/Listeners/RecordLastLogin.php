<?php

namespace App\Listeners;

use App\Models\User;
use Illuminate\Auth\Events\Login;

/**
 * Пишет время входа в users.last_login_at.
 *
 * Колонка существовала с самого начала, но её никто не заполнял, поэтому
 * «активных за 30 дней» в админке всегда показывало ноль. Слушаем событие
 * Login, а не контроллер входа: так учитываются все способы входа —
 * форма, «запомнить меня» и будущие токены API.
 */
class RecordLastLogin
{
    public function handle(Login $event): void
    {
        if (! $event->user instanceof User) {
            return;
        }

        // previous_login_at — старое значение last_login_at, до перезаписи:
        // без сдвига «сколько дней назад заходил» посчитать нечем, т.к.
        // last_login_at к моменту рендера дашборда уже равен текущему
        // визиту (см. PhilGreetingService, приветствие на дашборде).
        //
        // updateQuietly: обычный save дёрнул бы наблюдателей и обновил
        // updated_at, из-за чего «недавно изменённые» превратились бы
        // в «недавно заходившие».
        $event->user->forceFill([
            'previous_login_at' => $event->user->last_login_at,
            'last_login_at' => now(),
        ])->saveQuietly();
    }
}
