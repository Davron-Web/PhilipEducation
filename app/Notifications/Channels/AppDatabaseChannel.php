<?php

namespace App\Notifications\Channels;

use App\Models\System\Notification as StoredNotification;
use Illuminate\Notifications\Notification;

/**
 * Канал «database», пишущий в собственную таблицу notifications проекта.
 *
 * Штатный канал Laravel ожидает свою схему (uuid, type, notifiable_type,
 * data json). Здесь таблица другая — title/message/is_read — и на ней уже
 * держатся список уведомлений, счётчик непрочитанных в шапке и сидер.
 * Переезд на схему Laravel сломал бы всё это и потерял данные, поэтому
 * берём систему уведомлений фреймворка, но храним по-своему: классы
 * уведомлений, очереди и другие каналы (mail и будущие) работают как есть.
 */
class AppDatabaseChannel
{
    public function send(mixed $notifiable, Notification $notification): ?StoredNotification
    {
        if (! method_exists($notification, 'toAppDatabase')) {
            return null;
        }

        /** @var array{title: string, message: string} $payload */
        $payload = $notification->toAppDatabase($notifiable);

        if (! isset($notifiable->id)) {
            return null;
        }

        return StoredNotification::create([
            'user_id' => $notifiable->id,
            'title' => $payload['title'],
            'message' => $payload['message'],
            'is_read' => false,
        ]);
    }
}
