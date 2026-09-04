<?php

namespace App\Notifications;

use App\Models\Billing\Invoice;
use Illuminate\Notifications\Messages\MailMessage;

class PaymentSucceeded extends BaseNotification
{
    public function __construct(private readonly Invoice $invoice) {}

    /** Подтверждение оплаты — тот случай, когда письмо обязательно. */
    protected function sendsMail(): bool
    {
        return true;
    }

    public function toAppDatabase(object $notifiable): array
    {
        return [
            'title' => 'Оплата прошла',
            'message' => 'Тариф «'.$this->invoice->plan_name.'» на сумму '
                .$this->amount().'. Счёт '.$this->invoice->number.'.',
        ];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Оплата прошла — счёт '.$this->invoice->number)
            ->greeting('Здравствуйте, '.$notifiable->name.'!')
            ->line('Мы получили оплату тарифа «'.$this->invoice->plan_name.'» на сумму '.$this->amount().'.')
            ->line('Номер счёта: '.$this->invoice->number.'.')
            ->action('Перейти к обучению', url('/user/dashboard'))
            ->line('Спасибо, что учитесь с нами.');
    }

    private function amount(): string
    {
        return number_format($this->invoice->amount_minor / 100, 2, ',', ' ').' '.$this->invoice->currency;
    }
}
