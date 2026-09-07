<?php

test('registration screen can be rendered', function () {
    $response = $this->get('/register');

    $response->assertStatus(200);
});

test('new users can register', function () {
    $response = $this->post('/register', [
        'name' => 'tests users',
        // Не example.com: у него «null MX» (RFC 7505), домен официально
        // не принимает почту, и правило email:rfc,dns его отклоняет.
        'email' => 'tests.users@gmail.com',
        'password' => 'Tr0ubad0ur-x91k',
        'password_confirmation' => 'Tr0ubad0ur-x91k',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('user.dashboard', absolute: false));

    $user = auth()->user();
    expect($user->role)->not->toBeNull();
    expect($user->role->name)->toBe('student');
});
