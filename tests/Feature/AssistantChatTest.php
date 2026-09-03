<?php

use App\Models\User;
use App\Models\User\Role;
use App\Services\GeminiService;

// Phil закрыт подпиской, поэтому тестовому ученику её выдаём.
$student = fn () => tap(
    User::factory()->create(['role_id' => Role::factory()->student()->create()->id]),
    fn ($user) => giveSubscription($user),
);

it('blocks guests from the assistant chat endpoint', function () {
    $this->postJson('/assistant/chat', ['question' => 'Hi'])->assertRedirect('/login');
});

it('lets an authenticated user chat with the assistant', function () use ($student) {
    $this->mock(GeminiService::class, function ($mock) {
        $mock->shouldReceive('answerQuestion')->once()->andReturn('Hello there!');
    });

    $this->actingAs($student())
        ->postJson('/assistant/chat', ['question' => 'How do I say hi?'])
        ->assertOk()
        ->assertJson(['answer' => 'Hello there!']);
});

it('throttles the assistant chat endpoint after 20 requests per minute', function () use ($student) {
    $this->mock(GeminiService::class, function ($mock) {
        $mock->shouldReceive('answerQuestion')->times(20)->andReturn('ok');
    });

    $user = $this->actingAs($student());

    for ($i = 0; $i < 20; $i++) {
        $this->postJson('/assistant/chat', ['question' => "q{$i}"])->assertOk();
    }

    $this->postJson('/assistant/chat', ['question' => 'one too many'])
        ->assertStatus(429);
});
