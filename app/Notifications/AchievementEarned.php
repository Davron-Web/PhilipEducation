<?php

namespace App\Notifications;

use App\Models\Gamification\Achievement;

class AchievementEarned extends BaseNotification
{
    public function __construct(private readonly Achievement $achievement) {}

    public function toAppDatabase(object $notifiable): array
    {
        return [
            'title' => 'Новое достижение: '.$this->achievement->title,
            'message' => trim(($this->achievement->description ?? '').' +'.$this->achievement->points.' XP'),
        ];
    }
}
