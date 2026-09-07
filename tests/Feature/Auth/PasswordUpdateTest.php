<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;

test('password can be updated', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->from('/profiles')
        ->put('/password', [
            'current_password' => 'password',
            'password' => 'Tr0ubad0ur-x91k',
            'password_confirmation' => 'Tr0ubad0ur-x91k',
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect('/profiles');

    $this->assertTrue(Hash::check('Tr0ubad0ur-x91k', $user->refresh()->password));
});

test('correct password must be provided to update password', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->from('/profiles')
        ->put('/password', [
            'current_password' => 'wrong-password',
            'password' => 'Tr0ubad0ur-x91k',
            'password_confirmation' => 'Tr0ubad0ur-x91k',
        ]);

    $response
        ->assertSessionHasErrorsIn('updatePassword', 'current_password')
        ->assertRedirect('/profiles');
});
