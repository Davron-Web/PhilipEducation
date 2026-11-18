<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Раз в час: статус подписки должен соответствовать её сроку, иначе
// отчёты по активным подпискам считают уже закончившиеся.
Schedule::command('subscriptions:expire')->hourly();

// Предупреждение о конце подписки — раз в сутки, до начала рабочего дня.
Schedule::command('subscriptions:notify-expiring')->dailyAt('09:00');

// Напоминание тем, кто давно не заходил.
Schedule::command('students:remind')->dailyAt('10:00');

// Боты наполняют общий чат — раз в 2 минуты одна реплика от случайного бота.
Schedule::command('chat:bots')->everyTwoMinutes();

// Снимок мест в рейтинге — раз в сутки, до начала активности пользователей.
// Нужен для колонки изменения места (LeaderboardService::changeFor()):
// без вчерашнего снимка сравнивать текущее место не с чем.
Schedule::command('leaderboard:snapshot')->dailyAt('00:05');
