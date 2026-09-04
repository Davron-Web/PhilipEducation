<?php

namespace App\Notifications;

use App\Notifications\Channels\AppDatabaseChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

/**
 * Общая основа для уведомлений платформы.
 *
 * Каналы задаются здесь одним местом: своя запись в БД — всегда, письмо —
 * только если оно у уведомления предусмотрено. Добавить SMS или push потом
 * можно правкой одного метода via().
 *
 * Сознательно НЕ ShouldQueue. QUEUE_CONNECTION=database, и без запущенного
 * воркера queue:work очередь никто не разбирает — уведомления просто копились
 * бы в таблице jobs, а ученик не видел бы ничего. Запись в БД дешёвая, а
 * письма сейчас уходят в лог. Когда появится настоящий SMTP и постоянный
 * воркер, достаточно добавить сюда `implements ShouldQueue`.
 */
abstract class BaseNotification extends Notification
{
    use Queueable;

    /** @return array<int, string> */
    public function via(object $notifiable): array
    {
        $channels = [AppDatabaseChannel::class];

        if ($this->sendsMail() && filled($notifiable->email ?? null)) {
            $channels[] = 'mail';
        }

        return $channels;
    }

    /** Письмо шлём только для важного: достижения не должны засорять почту. */
    protected function sendsMail(): bool
    {
        return false;
    }

    /** @return array{title: string, message: string} */
    abstract public function toAppDatabase(object $notifiable): array;
}
