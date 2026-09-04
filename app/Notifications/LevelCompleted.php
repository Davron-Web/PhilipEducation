<?php

namespace App\Notifications;

/**
 * Курсов в проекте нет — материал разбит по уровням CEFR, поэтому
 * «завершение курса» здесь означает пройденные уроки одного уровня.
 */
class LevelCompleted extends BaseNotification
{
    public function __construct(
        private readonly string $levelName,
        private readonly int $lessonsCount,
    ) {}

    protected function sendsMail(): bool
    {
        return true;
    }

    public function toAppDatabase(object $notifiable): array
    {
        return [
            'title' => 'Уровень '.$this->levelName.' пройден',
            'message' => 'Вы завершили все уроки уровня — это '.$this->lessonsCount.' занятий. Можно переходить дальше.',
        ];
    }

    public function toMail(object $notifiable): \Illuminate\Notifications\Messages\MailMessage
    {
        return (new \Illuminate\Notifications\Messages\MailMessage)
            ->subject('Уровень '.$this->levelName.' пройден')
            ->greeting('Поздравляем, '.$notifiable->name.'!')
            ->line('Вы завершили все уроки уровня '.$this->levelName.' — это '.$this->lessonsCount.' занятий.')
            ->action('Продолжить обучение', url('/user/dashboard'))
            ->line('Так держать.');
    }
}
