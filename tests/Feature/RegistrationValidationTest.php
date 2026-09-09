<?php

use App\Models\User;

/**
 * Проверка адреса при регистрации.
 *
 * Подтверждение почты отключено, и эти правила — единственное, что теперь
 * отсекает выдуманные адреса: MX-запись домена и список одноразовых почт.
 * Поэтому тесты живут отдельно от парковки с тестами подтверждения.
 */
it('отклоняет несуществующий домен понятным сообщением', function () {
    $this->post('/register', [
        'name' => 'Кто-то',
        'email' => 'user@nonexistent-domain-zzz999123.com',
        'password' => 'Tr0ubad0ur-x91k',
        'password_confirmation' => 'Tr0ubad0ur-x91k',
    ])->assertSessionHasErrors(['email' => 'Такой почтовый домен не существует. Проверьте адрес — возможно, опечатка в части после «@».']);

    expect(User::where('email', 'like', '%zzz999123%')->exists())->toBeFalse();
});

it('отклоняет домен без почтового сервера', function () {
    // У example.com «null MX» (RFC 7505): домен официально не принимает
    // почту, и адрес на нём принимать бессмысленно.
    $this->post('/register', [
        'name' => 'Кто-то',
        'email' => 'someone@example.com',
        'password' => 'Tr0ubad0ur-x91k',
        'password_confirmation' => 'Tr0ubad0ur-x91k',
    ])->assertSessionHasErrors('email');
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

it('принимает адрес на живом домене', function () {
    $this->post('/register', [
        'name' => 'Живой Домен',
        'email' => 'valid.domain.check@gmail.com',
        'password' => 'Tr0ubad0ur-x91k',
        'password_confirmation' => 'Tr0ubad0ur-x91k',
    ])->assertSessionHasNoErrors();

    expect(User::where('email', 'valid.domain.check@gmail.com')->exists())->toBeTrue();
});

it('не требует подтверждения почты после регистрации', function () {
    $this->post('/register', [
        'name' => 'Сразу В Работу',
        'email' => 'no.verification.needed@gmail.com',
        'password' => 'Tr0ubad0ur-x91k',
        'password_confirmation' => 'Tr0ubad0ur-x91k',
    ]);

    $user = User::where('email', 'no.verification.needed@gmail.com')->firstOrFail();

    // Подтверждение отключено: разделы должны открываться сразу.
    foreach (['/lessons', '/words', '/tests'] as $url) {
        $this->actingAs($user)->get($url)->assertOk();
    }
});
