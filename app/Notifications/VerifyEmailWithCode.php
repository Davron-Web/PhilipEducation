<?php

namespace App\Notifications;

use App\Services\EmailVerificationCodeService;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\URL;

/**
 * Письмо с кодом подтверждения — и со ссылкой как запасным путём.
 *
 * Оба способа оставлены сознательно и ведут к одному результату: на
 * телефоне удобнее переписать шесть цифр в открытую вкладку, а с почты на
 * компьютере — нажать одну ссылку. Ссылка уже работала и покрыта тестами,
 * убирать рабочий путь было бы потерей без выигрыша.
 */
class VerifyEmailWithCode extends VerifyEmail
{
    public function __construct(private readonly string $code) {}

    public function toMail($notifiable): MailMessage
    {
        $site = config('app.name');
        $minutes = EmailVerificationCodeService::LIFETIME_MINUTES;

        return (new MailMessage)
            ->subject($site.' — код подтверждения '.$this->code)
            ->greeting('Здравствуйте, '.$notifiable->name.'!')
            ->line('Вы зарегистрировались на платформе '.$site.'. Введите этот код на сайте:')
            // Код отдельной строкой и крупно: с телефона его выделяют
            // пальцем, и соседний текст мешает попасть точно.
            ->line('# '.$this->code)
            ->line('Код действует '.$minutes.' минут.')
            ->action('Либо подтвердите одной кнопкой', $this->verificationUrl($notifiable))
            ->line('Если вы не регистрировались — просто удалите это письмо, ничего не произойдёт.')
            ->salutation('С уважением, команда '.$site);
    }

    /**
     * Ссылка собирается тем же способом, что и раньше.
     *
     * Копия из родительского класса: там метод protected и берёт срок из
     * той же настройки, но переопределить toMail, не тронув его, нельзя.
     */
    protected function verificationUrl($notifiable): string
    {
        return URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(config('auth.verification.expire', 60)),
            [
                'id' => $notifiable->getKey(),
                'hash' => sha1($notifiable->getEmailForVerification()),
            ]
        );
    }
}
