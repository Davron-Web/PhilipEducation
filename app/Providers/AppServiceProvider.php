<?php

namespace App\Providers;

use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\URL;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrapFive();

        $this->configureVerificationEmail();
        $this->configurePasswordRules();
        $this->configureUrlScheme();
    }

    /**
     * Схема адресов, которые собираются вне HTTP-запроса.
     *
     * Внутри запроса схему определяет сам запрос — после TrustProxies это
     * уже https за туннелем и за nginx. Но письма из очереди, команды
     * планировщика и напоминания собираются без запроса и берут схему из
     * APP_URL, поэтому здесь мы просто приводим одно к другому.
     *
     * Условие по APP_URL, а не по request()->isSecure(): провайдеры
     * загружаются раньше middleware, и на этом этапе X-Forwarded-Proto ещё
     * не прочитан — такая проверка всегда давала бы false и не сработала бы
     * ни разу. На локальном http://localhost условие ложно, и ссылки
     * остаются http, как и было.
     */
    private function configureUrlScheme(): void
    {
        if (str_starts_with((string) config('app.url'), 'https://')) {
            URL::forceScheme('https');
        }
    }

    /**
     * Требования к паролю — одни и те же везде: регистрация, смена пароля,
     * восстановление, создание пользователя админом.
     *
     * uncompromised() сверяет пароль со списком утечек через Have I Been
     * Pwned, отправляя только первые пять символов хеша — сам пароль наружу
     * не уходит. Если сервис недоступен, правило пропускает пароль: запирать
     * регистрацию из-за чужого сбоя хуже, чем пропустить слабый пароль.
     */
    private function configurePasswordRules(): void
    {
        Password::defaults(fn () => Password::min(8)->uncompromised());
    }

    /**
     * Письмо с подтверждением почты — на русском и от имени платформы.
     *
     * Стандартное письмо Laravel приходит по-английски и подписано именем
     * приложения из конфига; ученику, который только что зарегистрировался
     * на русскоязычном сайте, оно выглядит как чужая рассылка.
     *
     * Ссылку по-прежнему собирает сам фреймворк: подписанный адрес зависит
     * от домена и срока действия, и собирать его вручную значит однажды
     * разойтись с проверкой на другой стороне.
     */
    private function configureVerificationEmail(): void
    {
        VerifyEmail::toMailUsing(function (object $notifiable, string $url): MailMessage {
            $site = config('app.name');

            return (new MailMessage)
                ->subject($site.' — подтвердите адрес почты')
                ->greeting('Здравствуйте, '.$notifiable->name.'!')
                ->line('Вы зарегистрировались на платформе '.$site.'. Остался один шаг: подтвердите, что это ваш адрес.')
                ->action('Подтвердить почту', $url)
                ->line('Ссылка действует '.config('auth.verification.expire', 60).' минут.')
                ->line('Если вы не регистрировались — просто удалите это письмо, ничего не произойдёт.')
                ->salutation('С уважением, команда '.$site);
        });
    }
}
