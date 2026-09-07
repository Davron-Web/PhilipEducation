<?php

namespace App\Console\Commands\Mail;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

/**
 * Проверка почты одной командой.
 *
 * Нужна, чтобы отделять «сайт не отправил» от «письмо не дошло»: команда
 * показывает настройки, отправляет письмо и печатает Message-ID, который
 * присвоил сервер. Есть Message-ID — письмо принято, и искать надо уже в
 * ящике получателя, а не в коде.
 */
class SendTestMail extends Command
{
    protected $signature = 'mail:test {email : адрес получателя}';

    protected $description = 'Отправить проверочное письмо и показать результат';

    public function handle(): int
    {
        $to = $this->argument('email');

        if (! filter_var($to, FILTER_VALIDATE_EMAIL)) {
            $this->error("«{$to}» — не похоже на адрес почты.");

            return self::FAILURE;
        }

        $this->line('Настройки:');
        $this->line('  драйвер:    '.config('mail.default'));

        if (config('mail.default') === 'smtp') {
            $this->line('  сервер:     '.config('mail.mailers.smtp.host').':'.config('mail.mailers.smtp.port'));
            $this->line('  логин:      '.(config('mail.mailers.smtp.username') ?: '(пусто)'));
            // Пароль не печатаем — только факт, что он задан.
            $this->line('  пароль:     '.(config('mail.mailers.smtp.password') ? 'задан' : '(ПУСТО)'));
        }

        $this->line('  отправитель: '.config('mail.from.address'));

        if (config('mail.default') === 'log') {
            $this->warn('Драйвер «log»: письмо не уйдёт, а попадёт в storage/logs/laravel.log.');
        }

        if (config('mail.from.address') === $to) {
            $this->warn('Отправитель и получатель совпадают — Gmail показывает такое письмо в «Отправленных», а не во «Входящих».');
        }

        $this->newLine();
        $started = microtime(true);

        try {
            Mail::raw(
                'Проверочное письмо с сайта '.config('app.name').'. Если оно пришло — отправка настроена верно.',
                fn ($message) => $message->to($to)->subject(config('app.name').' — проверка почты')
            );
        } catch (\Throwable $e) {
            $this->error(sprintf('Отправка упала за %.1f с:', microtime(true) - $started));
            $this->line('  '.$e->getMessage());

            return self::FAILURE;
        }

        $this->info(sprintf('Письмо принято сервером за %.1f с.', microtime(true) - $started));
        $this->line('Подробности с Message-ID — в storage/logs/laravel.log.');

        return self::SUCCESS;
    }
}
