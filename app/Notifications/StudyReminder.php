<?php

namespace App\Notifications;

class StudyReminder extends BaseNotification
{
    public function __construct(private readonly int $daysAway) {}

    public function toAppDatabase(object $notifiable): array
    {
        return [
            'title' => 'Пора вернуться к занятиям',
            'message' => 'Вас не было '.$this->daysAway.' дней. Один урок сегодня — и серия начнётся заново.',
        ];
    }
}
