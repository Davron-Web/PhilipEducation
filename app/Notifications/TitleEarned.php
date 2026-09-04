<?php

namespace App\Notifications;

use App\Models\Gamification\Title;

class TitleEarned extends BaseNotification
{
    public function __construct(private readonly Title $title) {}

    public function toAppDatabase(object $notifiable): array
    {
        return [
            'title' => 'Новое звание: '.$this->title->name,
            'message' => $this->title->description
                ?: 'Вы набрали '.number_format($this->title->min_xp, 0, ',', ' ').' XP.',
        ];
    }
}
