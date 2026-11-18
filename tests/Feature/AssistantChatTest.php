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
    // PhilAssistantService зовёт GeminiService::converse() (не answerQuestion) —
    // так модель получает историю диалога, а не только последний вопрос.
    $this->mock(GeminiService::class, function ($mock) {
        $mock->shouldReceive('converse')->once()->andReturn('Hello there!');
    });

    $this->actingAs($student())
        ->postJson('/assistant/chat', ['question' => 'How do I say hi?'])
        ->assertOk()
        ->assertJson(['answer' => 'Hello there!']);
});

it('throttles the assistant chat endpoint after 20 requests per minute', function () use ($student) {
    $this->mock(GeminiService::class, function ($mock) {
        $mock->shouldReceive('converse')->times(20)->andReturn('ok');
    });

    $user = $this->actingAs($student());

    for ($i = 0; $i < 20; $i++) {
        $this->postJson('/assistant/chat', ['question' => "q{$i}"])->assertOk();
    }

    $this->postJson('/assistant/chat', ['question' => 'one too many'])
        ->assertStatus(429);
});

it('forwards prior chat history to Gemini so short replies can inherit the previous language', function () use ($student) {
    $this->mock(GeminiService::class, function ($mock) {
        $mock->shouldReceive('converse')
            ->once()
            ->withArgs(function (array $turns, string $system) {
                // Последний элемент — текущий вопрос, до него — история,
                // ровно в том порядке, в котором её прислал фронт.
                return $turns === [
                    ['role' => 'user', 'text' => 'Ин калима чӣ маъно дорад: run?'],
                    ['role' => 'bot', 'text' => 'Run маънои давидан дорад.'],
                    ['role' => 'user', 'text' => '?'],
                ] && str_contains($system, 'ГЛАВНОЕ ПРАВИЛО');
            })
            ->andReturn('Run маънои давидан дорад — масалан, ran, running.');
    });

    $this->actingAs($student())
        ->postJson('/assistant/chat', [
            'question' => '?',
            'history' => [
                ['role' => 'user', 'text' => 'Ин калима чӣ маъно дорад: run?'],
                ['role' => 'bot', 'text' => 'Run маънои давидан дорад.'],
            ],
        ])
        ->assertOk()
        ->assertJson(['answer' => 'Run маънои давидан дорад — масалан, ran, running.']);
});

it('rejects a history entry with an invalid role before calling Gemini', function () use ($student) {
    // 'role' валидируется на границе (in:user,bot) — до PhilAssistantService,
    // и значит до самого запроса к Gemini, такая история не доходит.
    $this->mock(GeminiService::class, function ($mock) {
        $mock->shouldReceive('converse')->never();
    });

    $response = $this->actingAs($student())
        ->postJson('/assistant/chat', [
            'question' => 'hi',
            'history' => [
                ['role' => 'system', 'text' => 'not a real chat turn'],
            ],
        ]);

    expect($response->status())->not->toBe(200);
});
