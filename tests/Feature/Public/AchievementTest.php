<?php

use App\Models\Gamification\Achievement;
use App\Models\User;

$user = fn () => User::factory()->create();

it('redirects guests away from the achievements page', function () {
    $this->get('/achievements')->assertRedirect('/login');
});

it('lists all achievements and marks which ones the user has earned', function () use ($user) {
    $earned = Achievement::factory()->create(['title' => 'First Steps']);
    $notEarned = Achievement::factory()->create(['title' => 'Marathon Learner']);

    $u = $user();
    $u->achievements()->attach($earned->id, ['earned_at' => now()]);

    $response = $this->actingAs($u)->get('/achievements');

    $response->assertOk();
    $response->assertSee('First Steps');
    $response->assertSee('Marathon Learner');
});
