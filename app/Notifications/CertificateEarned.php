<?php

namespace App\Notifications;

use App\Models\Certificate\Certificate;
use Illuminate\Notifications\Messages\MailMessage;

/**
 * Получение сертификата уровня — более весомое событие, чем обычное
 * достижение (AchievementEarned), поэтому, в отличие от него, шлём и
 * письмо, тем же принципом, что и LevelCompleted.
 */
class CertificateEarned extends BaseNotification
{
    public function __construct(private readonly Certificate $certificate) {}

    protected function sendsMail(): bool
    {
        return true;
    }

    public function toAppDatabase(object $notifiable): array
    {
        $levelName = $this->certificate->level?->name ?? $this->certificate->level?->code ?? '';

        return [
            'title' => 'Сертификат уровня '.$levelName.' получен',
            'message' => 'Вы выполнили все условия уровня — уроки, тесты и слова — и получили сертификат '.$this->certificate->certificate_number.'.',
        ];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $levelName = $this->certificate->level?->name ?? $this->certificate->level?->code ?? '';

        return (new MailMessage)
            ->subject('Сертификат уровня '.$levelName.' получен')
            ->greeting('Поздравляем, '.$notifiable->name.'!')
            ->line('Вы выполнили все условия уровня '.$levelName.' — уроки, тесты и слова — и получили сертификат.')
            ->line('Номер сертификата: '.$this->certificate->certificate_number)
            ->action('Посмотреть сертификат', url('/achievements'))
            ->line('Так держать!');
    }
}
