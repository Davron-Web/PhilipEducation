<?php

use App\Models\Content\Lesson;
use App\Models\System\Level;
use App\Models\Test\Test;
use App\Models\User;
use App\Models\User\Role;
use App\Models\Vocabulary\Word;
use Illuminate\Auth\Events\Verified;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;

function verifiedStudent(): User
{
    return User::factory()->create([
        'role_id' => Role::factory()->student()->create()->id,
        'is_active' => true,
        'email_verified_at' => now(),
    ]);
}

function unverifiedStudent(): User
{
    return User::factory()->unverified()->create([
        'role_id' => Role::factory()->student()->create()->id,
        'is_active' => true,
    ]);
}

// ─── Сценарий 1: регистрация с настоящей почтой ──────────────────────────

it('регистрирует с настоящим доменом и шлёт письмо на русском', function () {
    Notification::fake();

    $this->post('/register', [
        'name' => 'Новый ученик',
        'email' => 'new.student.check@gmail.com',
        'password' => 'Tr0ubad0ur-x91k',
        'password_confirmation' => 'Tr0ubad0ur-x91k',
    ])->assertRedirect();

    $user = User::where('email', 'new.student.check@gmail.com')->firstOrFail();

    expect($user->hasVerifiedEmail())->toBeFalse();

    Notification::assertSentTo($user, VerifyEmail::class, function ($notification) use ($user) {
        $mail = $notification->toMail($user);

        return str_contains($mail->subject, config('app.name'))
            && str_contains($mail->subject, 'подтвердите')
            && str_contains(implode(' ', $mail->introLines), 'зарегистрировались');
    });
});

it('подтверждает почту по ссылке из письма', function () {
    Event::fake();
    $user = unverifiedStudent();

    $url = URL::temporarySignedRoute('verification.verify', now()->addMinutes(60), [
        'id' => $user->id,
        'hash' => sha1($user->email),
    ]);

    $this->actingAs($user)->get($url)->assertRedirect();

    Event::assertDispatched(Verified::class);
    expect($user->fresh()->hasVerifiedEmail())->toBeTrue();
});

it('не подтверждает по подделанной ссылке', function () {
    $user = unverifiedStudent();

    // Без подписи ссылку можно было бы собрать руками для чужого id.
    $this->actingAs($user)
        ->get('/verify-email/'.$user->id.'/'.sha1('wrong@example.com'))
        ->assertForbidden();

    expect($user->fresh()->hasVerifiedEmail())->toBeFalse();
});

// ─── Сценарий 2: выдуманный домен и одноразовая почта ────────────────────

it('отклоняет несуществующий домен понятным сообщением', function () {
    $this->post('/register', [
        'name' => 'Кто-то',
        'email' => 'user@nonexistent-domain-zzz999123.com',
        'password' => 'Tr0ubad0ur-x91k',
        'password_confirmation' => 'Tr0ubad0ur-x91k',
    ])->assertSessionHasErrors(['email' => 'Такой почтовый домен не существует. Проверьте адрес — возможно, опечатка в части после «@».']);

    expect(User::where('email', 'like', '%zzz999123%')->exists())->toBeFalse();
});

it('отклоняет одноразовую почту', function () {
    $this->post('/register', [
        'name' => 'Кто-то',
        'email' => 'throwaway@mailinator.com',
        'password' => 'Tr0ubad0ur-x91k',
        'password_confirmation' => 'Tr0ubad0ur-x91k',
    ])->assertSessionHasErrors('email');

    expect(User::where('email', 'throwaway@mailinator.com')->exists())->toBeFalse();
});

it('отклоняет поддомен одноразового сервиса', function () {
    // Сервисы раздают адреса вида user@team.mailinator.com.
    $this->post('/register', [
        'name' => 'Кто-то',
        'email' => 'a@team.mailinator.com',
        'password' => 'Tr0ubad0ur-x91k',
        'password_confirmation' => 'Tr0ubad0ur-x91k',
    ])->assertSessionHasErrors('email');
});

it('берёт список одноразовых доменов из конфига', function () {
    config(['disposable_email.blocked_domains' => ['gmail.com']]);

    // Список должен пополняться правкой конфига, а не кода.
    $this->post('/register', [
        'name' => 'Кто-то',
        'email' => 'someone@gmail.com',
        'password' => 'Tr0ubad0ur-x91k',
        'password_confirmation' => 'Tr0ubad0ur-x91k',
    ])->assertSessionHasErrors('email');
});

// ─── Сценарий 3: старые пользователи и доступ к разделам ─────────────────

it('пускает подтверждённого ученика в уроки, словарь и тесты', function () {
    $user = verifiedStudent();
    $level = Level::factory()->create(['code' => 'A1']);
    Lesson::factory()->create(['is_published' => true, 'level_id' => $level->id]);
    Word::factory()->create();
    Test::factory()->create(['is_published' => true]);

    foreach (['/lessons', '/words', '/tests'] as $url) {
        $this->actingAs($user)->get($url)->assertOk();
    }
});

it('не пускает неподтверждённого и объясняет почему', function () {
    $user = unverifiedStudent();

    foreach (['/lessons', '/words', '/tests'] as $url) {
        $this->actingAs($user)->get($url)->assertRedirect(route('verification.notice'));
    }

    $this->actingAs($user)
        ->get(route('verification.notice'))
        ->assertOk()
        ->assertSee('Подтвердите почту')
        ->assertSee('Отправить письмо ещё раз');
});

it('закрывает и запасной путь /public/lessons', function () {
    $user = unverifiedStudent();
    $lesson = Lesson::factory()->create(['is_published' => true]);

    // Тот же материал был доступен по второму адресу вообще без входа —
    // иначе ограничение обходилось сменой префикса в строке браузера.
    $this->get("/public/lessons/{$lesson->id}")->assertRedirect('/login');
    $this->actingAs($user)->get("/public/lessons/{$lesson->id}")->assertRedirect(route('verification.notice'));
});

it('оставляет открытыми профиль и выход', function () {
    $user = unverifiedStudent();

    // Иначе человек не сможет исправить опечатку в адресе.
    $this->actingAs($user)->get('/profiles/edit')->assertOk();
});

// ─── Защита от рассылки ──────────────────────────────────────────────────

it('не даёт слать письма чаще раза в минуту', function () {
    Notification::fake();
    $user = unverifiedStudent();

    $this->actingAs($user)->post('/email/verification-notification')->assertRedirect();
    $this->actingAs($user)->post('/email/verification-notification')->assertStatus(429);

    // Иначе форму «отправить ещё раз» можно нажимать подряд и рассылать
    // письма с нашего SMTP.
    Notification::assertSentToTimes($user, VerifyEmail::class, 1);
});
