<?php

namespace App\Notifications;

use App\Models\Billing\Subscription;
use Illuminate\Notifications\Messages\MailMessage;

class SubscriptionExpiring extends BaseNotification
{
    public function __construct(
        private readonly Subscription $subscription,
        private readonly int $daysLeft,
    ) {}

    /** О конце оплаченного доступа человек должен узнать заранее и наверняка. */
    protected function sendsMail(): bool
    {
        return true;
    }

    public function toAppDatabase(object $notifiable): array
    {
        return [
            'title' => 'Подписка заканчивается',
            'message' => 'Доступ действует ещё '.$this->daysLeft.' '.$this->daysWord()
                .' — до '.$this->subscription->ends_at->format('d.m.Y').'. Продлите, чтобы не потерять уроки.',
        ];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Подписка заканчивается через '.$this->daysLeft.' '.$this->daysWord())
            ->greeting('Здравствуйте, '.$notifiable->name.'!')
            ->line('Ваш доступ действует до '.$this->subscription->ends_at->format('d.m.Y').'.')
            ->action('Продлить подписку', url('/plans'))
            ->line('После этой даты платные разделы закроются, но прогресс сохранится.');
    }

    /** 1 день / 2 дня / 5 дней */
    private function daysWord(): string
    {
        $mod100 = $this->daysLeft % 100;
        $mod10 = $this->daysLeft % 10;

        if ($mod100 >= 11 && $mod100 <= 14) {
            return 'дней';
        }

        return match (true) {
            $mod10 === 1 => 'день',
            $mod10 >= 2 && $mod10 <= 4 => 'дня',
            default => 'дней',
        };
    }
}
