<?php

use App\Models\Auth\EmailVerificationCode;
use App\Models\User;
use App\Models\User\Role;
use App\Notifications\VerifyEmailWithCode;
use App\Services\EmailVerificationCodeService;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;

beforeEach(fn () => cache()->flush());

function codeStudent(): User
{
    return User::factory()->unverified()->create([
        'role_id' => Role::factory()->student()->create()->id,
        'is_active' => true,
    ]);
}

/** Выдаёт код и возвращает его — как это делает отправка письма. */
function issueCode(User $user): string
{
    return app(EmailVerificationCodeService::class)->issue($user);
}

it('верный код подтверждает почту', function () {
    Event::fake();
    $user = codeStudent();
    $code = issueCode($user);

    $this->actingAs($user)
        ->post('/verify-email/code', ['code' => $code])
        ->assertRedirect();

    expect($user->fresh()->hasVerifiedEmail())->toBeTrue()
        // Код одноразовый: после успеха запись удаляется.
        ->and(EmailVerificationCode::where('user_id', $user->id)->exists())->toBeFalse();

    Event::assertDispatched(Verified::class);
});

it('неверный код не подтверждает и считает попытки', function () {
    $user = codeStudent();
    $code = issueCode($user);
    $wrong = str_pad((string) ((((int) $code) + 1) % 1000000), 6, '0', STR_PAD_LEFT);

    $this->actingAs($user)
        ->post('/verify-email/code', ['code' => $wrong])
        ->assertSessionHasErrors('code');

    expect($user->fresh()->hasVerifiedEmail())->toBeFalse()
        ->and(EmailVerificationCode::where('user_id', $user->id)->value('attempts'))->toBe(1);
});

it('истёкший код отклоняется', function () {
    $user = codeStudent();
    $code = issueCode($user);

    // Срок жизни — 15 минут.
    $this->travel(EmailVerificationCodeService::LIFETIME_MINUTES + 1)->minutes();

    $this->actingAs($user)
        ->post('/verify-email/code', ['code' => $code])
        ->assertSessionHasErrors(['code' => 'Срок действия кода истёк — запросите новый.']);

    expect($user->fresh()->hasVerifiedEmail())->toBeFalse();
});

it('после пяти неверных попыток код сгорает', function () {
    $user = codeStudent();
    $code = issueCode($user);
    $wrong = str_pad((string) ((((int) $code) + 7) % 1000000), 6, '0', STR_PAD_LEFT);

    for ($i = 0; $i < EmailVerificationCodeService::MAX_ATTEMPTS; $i++) {
        $this->actingAs($user)->post('/verify-email/code', ['code' => $wrong]);
    }

    // Шесть цифр — миллион вариантов, без лимита их перебирают за вечер.
    expect(EmailVerificationCode::where('user_id', $user->id)->exists())->toBeFalse();

    // Даже верный код больше не работает — нужен новый.
    $this->actingAs($user)
        ->post('/verify-email/code', ['code' => $code])
        ->assertSessionHasErrors('code');

    expect($user->fresh()->hasVerifiedEmail())->toBeFalse();
});

it('ограничивает повторную отправку одним разом в минуту', function () {
    Notification::fake();
    $user = codeStudent();

    $this->actingAs($user)->post('/email/verification-notification')->assertRedirect();

    // Второе нажатие возвращает на ту же страницу со сроком ожидания,
    // а не страницу «429 Слишком много запросов»: из неё не понять,
    // что произошло и сколько ждать.
    $this->actingAs($user)->post('/email/verification-notification')
        ->assertRedirect()
        ->assertSessionHas('resend-wait');

    Notification::assertSentToTimes($user, VerifyEmailWithCode::class, 1);
});

it('новый код отменяет прежний', function () {
    $user = codeStudent();
    $first = issueCode($user);
    $second = issueCode($user);

    expect($first)->not->toBe($second);

    $this->actingAs($user)
        ->post('/verify-email/code', ['code' => $first])
        ->assertSessionHasErrors('code');

    $this->actingAs($user)
        ->post('/verify-email/code', ['code' => $second])
        ->assertRedirect();

    expect($user->fresh()->hasVerifiedEmail())->toBeTrue();
});

it('хранит хеш, а не сам код', function () {
    $user = codeStudent();
    $code = issueCode($user);

    $stored = EmailVerificationCode::where('user_id', $user->id)->value('code_hash');

    // В базе не должно быть готового ключа к чужому аккаунту.
    expect($stored)->not->toBe($code)
        ->and(strlen($stored))->toBe(64)
        ->and($stored)->toBe(hash('sha256', $code));
});

it('выдаёт шестизначный код с ведущими нулями', function () {
    $user = codeStudent();

    for ($i = 0; $i < 20; $i++) {
        $code = issueCode($user);
        expect($code)->toMatch('/^\d{6}$/');
    }
});

it('шлёт код в письме на русском с названием сайта', function () {
    Notification::fake();
    $user = codeStudent();

    $user->sendEmailVerificationNotification();

    Notification::assertSentTo($user, VerifyEmailWithCode::class, function ($notification) use ($user) {
        $mail = $notification->toMail($user);
        $body = implode(' ', $mail->introLines);
        $code = EmailVerificationCode::where('user_id', $user->id)->first();

        return str_contains($mail->subject, config('app.name'))
            && str_contains($body, 'Введите этот код')
            // Ссылка остаётся вторым способом и ведёт к тому же результату.
            && str_contains($mail->actionUrl, 'verify-email/')
            && $code !== null;
    });
});

it('показывает адрес и ссылку на его изменение', function () {
    $user = codeStudent();

    $this->actingAs($user)
        ->get(route('verification.notice'))
        ->assertOk()
        ->assertSee($user->email)
        ->assertSee('Изменить адрес')
        ->assertSee('inputmode="numeric"', escape: false)
        ->assertSee('autofocus', escape: false);
});

it('ссылка из письма по-прежнему подтверждает', function () {
    $user = codeStudent();
    issueCode($user);

    $url = URL::temporarySignedRoute('verification.verify', now()->addMinutes(60), [
        'id' => $user->id,
        'hash' => sha1($user->email),
    ]);

    // Оба способа обязаны вести к одному результату.
    $this->actingAs($user)->get($url)->assertRedirect();

    expect($user->fresh()->hasVerifiedEmail())->toBeTrue();
});

it('не принимает код не той длины', function () {
    $user = codeStudent();
    issueCode($user);

    foreach (['12345', '1234567', 'abcdef'] as $bad) {
        $this->actingAs($user)
            ->post('/verify-email/code', ['code' => $bad])
            ->assertSessionHasErrors('code');
    }

    // Отбраковка по формату не должна тратить попытки на настоящем коде.
    expect(EmailVerificationCode::where('user_id', $user->id)->value('attempts'))->toBe(0);
});
