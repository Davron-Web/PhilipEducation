<?php

use App\Models\User;

test('profile index page is displayed', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->get('/profiles');

    $response->assertOk();
});

test('profile edit page is displayed', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->get('/profiles/edit');

    $response->assertOk();
});

test('profile information can be updated', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->put('/profiles', [
            'name' => 'Test User Updated',
            'email' => 'updated@example.com',
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('profiles.edit'));

    $user->refresh();

    $this->assertSame('Test User Updated', $user->name);
    $this->assertSame('updated@example.com', $user->email);
});

test('profile update requires a valid email', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->put('/profiles', [
            'name' => 'Test User',
            'email' => 'not-an-email',
        ]);

    $response->assertSessionHasErrors('email');
});

test('guests cannot access the profile pages', function () {
    $this->get('/profiles')->assertRedirect('/login');
    $this->put('/profiles', [])->assertRedirect('/login');
});
