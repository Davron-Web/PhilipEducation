<?php

use App\Models\User;
use App\Models\User\Role;

it('redirects guests from the root page to login', function () {
    $response = $this->get('/');

    $response->assertRedirect('/login');
});

it('redirects authenticated students from the root page to their dashboard', function () {
    // The app treats role_id == 1 as admin, so create the admin role first
    // (claiming id 1) and put the test user on a distinct, non-admin role.
    Role::factory()->admin()->create();
    $studentRole = Role::factory()->student()->create();
    $user = User::factory()->create(['role_id' => $studentRole->id]);

    $response = $this->actingAs($user)->get('/');

    $response->assertRedirect(route('user.dashboard'));
});
