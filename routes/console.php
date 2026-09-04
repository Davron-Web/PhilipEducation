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
