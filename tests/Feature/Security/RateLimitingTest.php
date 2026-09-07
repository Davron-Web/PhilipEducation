<?php

use App\Models\User;
use App\Models\User\Role;

beforeEach(fn () => cache()->flush());

function rateLimitUser(string $email = 'victim@gmail.com'): User
{
    return User::factory()->create([
        'email' => $email,
        'password' => bcrypt('Tr0ubad0ur-x91k'),
        'role_id' => Role::factory()->student()->create()->id,
        'is_active' => true,
        'email_verified_at' => now(),
    ]);
}

it('блокирует подбор пароля после пяти попыток', function () {
    rateLimitUser();

    for ($i = 0; $i < 5; $i++) {
        $this->post('/login', ['email' => 'victim@gmail.com', 'password' => 'wrong'.$i]);
    }

    $response = $this->post('/login', ['email' => 'victim@gmail.com', 'password' => 'wrong-again']);

    // Шестая попытка не доходит до проверки пароля: либо throttle отдаёт 429,
    // либо LoginRequest возвращает ошибку со сроком ожидания.
    expect($response->status())->toBeIn([302, 429]);

    if ($response->status() === 302) {
        expect(implode(' ', session('errors')->get('email')))->toMatch('/\d+/');
    }
});

it('не запирает соседний аккаунт из-за чужих неудач', function () {
    rateLimitUser('one@gmail.com');
    rateLimitUser('two@gmail.com');

    for ($i = 0; $i < 3; $i++) {
        $this->post('/login', ['email' => 'one@gmail.com', 'password' => 'wrong']);
    }

    // Счётчик в LoginRequest ведётся по паре email+IP, поэтому неудачи по
    // одному адресу не должны мешать входу под другим.
    $this->post('/login', ['email' => 'two@gmail.com', 'password' => 'Tr0ubad0ur-x91k'])
        ->assertRedirect();

    $this->assertAuthenticated();
});

it('ограничивает регистрацию пятью запросами в минуту', function () {
    // Отправляем заведомо невалидные данные: аккаунт не создаётся и вход не
    // происходит, поэтому middleware guest не разворачивает следующий запрос
    // раньше, чем до него дойдёт счётчик. Именно так ведёт себя бот, который
    // перебирает адреса, а не регистрируется по-настоящему.
    for ($i = 0; $i < 5; $i++) {
        $this->post('/register', ['email' => "bot{$i}@gmail.com"]);
    }

    $this->post('/register', [
        'name' => 'Bot 6',
        'email' => 'bot6@gmail.com',
        'password' => 'Tr0ubad0ur-x91k',
        'password_confirmation' => 'Tr0ubad0ur-x91k',
    ])->assertStatus(429);

    expect(User::where('email', 'bot6@gmail.com')->exists())->toBeFalse();
});

it('ограничивает запрос ссылки для восстановления пароля', function () {
    rateLimitUser();

    for ($i = 0; $i < 5; $i++) {
        $this->post('/forgot-password', ['email' => 'victim@gmail.com']);
    }

    // Иначе форму можно использовать как рассыльщик через наш SMTP.
    $this->post('/forgot-password', ['email' => 'victim@gmail.com'])->assertStatus(429);
});

it('ограничивает установку нового пароля по токену', function () {
    for ($i = 0; $i < 5; $i++) {
        $this->post('/reset-password', [
            'token' => 'guess'.$i,
            'email' => 'victim@gmail.com',
            'password' => 'Tr0ubad0ur-x91k',
            'password_confirmation' => 'Tr0ubad0ur-x91k',
        ]);
    }

    // Без ограничения токен восстановления можно перебирать.
    $this->post('/reset-password', [
        'token' => 'guess-again',
        'email' => 'victim@gmail.com',
        'password' => 'Tr0ubad0ur-x91k',
        'password_confirmation' => 'Tr0ubad0ur-x91k',
    ])->assertStatus(429);
});

it('ограничивает смену пароля в настройках', function () {
    $user = rateLimitUser();

    for ($i = 0; $i < 5; $i++) {
        $this->actingAs($user)->put('/password', [
            'current_password' => 'wrong',
            'password' => 'Tr0ubad0ur-x91k',
            'password_confirmation' => 'Tr0ubad0ur-x91k',
        ]);
    }

    $this->actingAs($user)->put('/password', [
        'current_password' => 'wrong',
        'password' => 'Tr0ubad0ur-x91k',
        'password_confirmation' => 'Tr0ubad0ur-x91k',
    ])->assertStatus(429);
});

it('не принимает пароль из утечек', function () {
    foreach (['12345678', 'password', 'qwerty123'] as $weak) {
        $this->post('/register', [
            'name' => 'Кто-то',
            'email' => 'weak'.substr(md5($weak), 0, 6).'@gmail.com',
            'password' => $weak,
            'password_confirmation' => $weak,
        ])->assertSessionHasErrors('password');
    }

    expect(User::where('email', 'like', 'weak%')->exists())->toBeFalse();
});

it('принимает надёжный пароль', function () {
    $this->post('/register', [
        'name' => 'Strong Pass',
        'email' => 'strong.pass@gmail.com',
        'password' => 'Tr0ubad0ur-x91k',
        'password_confirmation' => 'Tr0ubad0ur-x91k',
    ])->assertSessionHasNoErrors();

    expect(User::where('email', 'strong.pass@gmail.com')->exists())->toBeTrue();
});
