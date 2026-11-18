<?php

use App\Models\Chat\ChatMessage;
use App\Models\Chat\ChatReport;
use App\Models\User;
use App\Models\User\Role;

$student = fn () => User::factory()->create(['role_id' => Role::factory()->student()->create()->id]);
$admin = fn () => User::factory()->create(['role_id' => Role::factory()->admin()->create()->id]);

it('blocks guests from the chat page and endpoints', function () {
    $this->get('/chat')->assertRedirect('/login');
    $this->postJson('/chat/messages', ['body' => 'hi'])->assertRedirect('/login');
});

it('lets an authenticated user send a message', function () use ($student) {
    $this->actingAs($student())
        ->postJson('/chat/messages', ['body' => 'Привет всем!'])
        ->assertCreated()
        ->assertJsonPath('message.body', 'Привет всем!')
        ->assertJsonPath('message.is_own', true);

    expect(ChatMessage::count())->toBe(1);
});

it('rejects an empty or whitespace-only message', function () use ($student) {
    $user = $student();

    $this->actingAs($user)->postJson('/chat/messages', ['body' => ''])->assertStatus(422);
    $this->actingAs($user)->postJson('/chat/messages', ['body' => '    '])->assertStatus(422);

    expect(ChatMessage::count())->toBe(0);
});

it('rejects a message longer than 500 characters', function () use ($student) {
    $this->actingAs($student())
        ->postJson('/chat/messages', ['body' => str_repeat('a', 501)])
        ->assertStatus(422);
});

it('rejects messages containing profanity', function () use ($student) {
    $this->actingAs($student())
        ->postJson('/chat/messages', ['body' => 'ты сука такой'])
        ->assertStatus(422)
        ->assertJsonValidationErrors('body');

    expect(ChatMessage::count())->toBe(0);
});

it('rejects links from regular users but allows them for admins', function () use ($student, $admin) {
    $this->actingAs($student())
        ->postJson('/chat/messages', ['body' => 'зайдите на example.com'])
        ->assertStatus(422)
        ->assertJsonValidationErrors('body');

    $this->actingAs($admin())
        ->postJson('/chat/messages', ['body' => 'зайдите на example.com'])
        ->assertCreated();

    expect(ChatMessage::count())->toBe(1);
});

it('throttles message sending after 5 per minute', function () use ($student) {
    $user = $this->actingAs($student());

    for ($i = 0; $i < 5; $i++) {
        $this->postJson('/chat/messages', ['body' => "message {$i}"])->assertCreated();
    }

    $this->postJson('/chat/messages', ['body' => 'one too many'])->assertStatus(429);
});

it('blocks a chat-banned user from sending but still shows the chat page', function () use ($student) {
    $user = $student();
    $user->update(['chat_banned_until' => now()->addDay()]);

    $this->actingAs($user)->get('/chat')->assertOk();
    $this->actingAs($user)->postJson('/chat/messages', ['body' => 'привет'])->assertStatus(403);

    expect(ChatMessage::count())->toBe(0);
});

it('poll only returns messages after the given id', function () use ($student) {
    $user = $student();
    $older = ChatMessage::factory()->for($user)->create();
    $newer = ChatMessage::factory()->for($user)->create();

    $this->actingAs($user)
        ->getJson('/chat/messages?after_id='.$older->id)
        ->assertOk()
        ->assertJsonCount(1, 'messages')
        ->assertJsonPath('messages.0.id', $newer->id);
});

it('lets the author delete their own message but not someone else\'s', function () use ($student) {
    $author = $student();
    $other = $student();
    $message = ChatMessage::factory()->for($author)->create();

    $this->actingAs($other)
        ->deleteJson("/chat/messages/{$message->id}")
        ->assertForbidden();

    $this->actingAs($author)
        ->deleteJson("/chat/messages/{$message->id}")
        ->assertOk();

    expect($message->fresh()->is_deleted)->toBeTrue();
});

it('lets an admin delete any message', function () use ($student, $admin) {
    $message = ChatMessage::factory()->for($student())->create();

    $this->actingAs($admin())
        ->deleteJson("/chat/messages/{$message->id}")
        ->assertOk();

    expect($message->fresh()->is_deleted)->toBeTrue();
});

it('records a report and silently no-ops on a duplicate report', function () use ($student) {
    $message = ChatMessage::factory()->for($student())->create();
    $reporter = $student();

    $this->actingAs($reporter)
        ->postJson("/chat/messages/{$message->id}/report")
        ->assertOk();

    // Повторная жалоба от того же человека на то же сообщение — не 500,
    // а тихий успех (unique(chat_message_id, reported_by) в БД).
    $this->actingAs($reporter)
        ->postJson("/chat/messages/{$message->id}/report")
        ->assertOk();

    expect(ChatReport::count())->toBe(1);
});
