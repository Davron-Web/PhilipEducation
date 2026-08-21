<?php

use App\Models\User;
use App\Models\User\Role;

// role_id == 1 is treated as admin app-wide, so always reserve id 1 for the
// admin role first — otherwise a lone "student" role created in an empty
// test database could land on id 1 and be misread as admin.
$adminRole = fn () => Role::firstOrCreate(['name' => 'admin'], ['description' => 'Администратор платформы']);
$studentRole = function () use ($adminRole) {
    $adminRole();

    return Role::firstOrCreate(['name' => 'student'], ['description' => 'Ученик платформы']);
};
$admin = fn () => User::factory()->create(['role_id' => $adminRole()->id]);
$student = fn () => User::factory()->create(['role_id' => $studentRole()->id]);

it('redirects guests away from the user dashboard', function () {
    $this->get('/user/dashboard')->assertRedirect('/login');
});

it('lets a student view the user dashboard', function () use ($student) {
    $this->actingAs($student())
        ->get('/user/dashboard')
        ->assertOk()
        ->assertViewIs('user.dashboard');
});

it('redirects guests away from the admin dashboard', function () {
    $this->get('/admin/dashboard')->assertRedirect('/login');
});

it('blocks a student from the admin dashboard', function () use ($student) {
    $this->actingAs($student())
        ->get('/admin/dashboard')
        ->assertForbidden();
});

it('lets an admin view the admin dashboard', function () use ($admin) {
    $this->actingAs($admin())
        ->get('/admin/dashboard')
        ->assertOk()
        ->assertViewIs('admin.dashboard');
});

it('routes the generic /dashboard alias by role', function () use ($student, $admin) {
    $this->actingAs($student())->get('/dashboard')->assertRedirect(route('user.dashboard'));
    $this->actingAs($admin())->get('/dashboard')->assertRedirect(route('admin.dashboard'));
});
