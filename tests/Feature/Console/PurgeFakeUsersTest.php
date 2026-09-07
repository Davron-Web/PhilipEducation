<?php

use App\Models\Content\Lesson;
use App\Models\User;
use App\Models\User\Role;
use App\Models\User\UserProgress;
use App\Models\Vocabulary\Word;

function fakeAccount(string $email): User
{
    $user = User::factory()->create([
        'email' => $email,
        'role_id' => Role::factory()->student()->create()->id,
        'email_verified_at' => now(),
    ]);

    UserProgress::create([
        'user_id' => $user->id,
        'lesson_id' => Lesson::factory()->create()->id,
        'progress_percent' => 100,
        'is_completed' => true,
    ]);

    $user->words()->attach(Word::factory()->create()->id, ['learned' => true]);

    return $user;
}

it('без --force ничего не удаляет', function () {
    fakeAccount('bot1@example.com');

    $this->artisan('users:purge-fake')
        ->expectsOutputToContain('Ничего не удалено')
        ->assertSuccessful();

    expect(User::where('email', 'bot1@example.com')->exists())->toBeTrue();
});

it('показывает настоящих пользователей до удаления', function () {
    fakeAccount('bot2@example.com');
    User::factory()->create(['email' => 'real.person@gmail.com', 'name' => 'Настоящий']);

    $this->artisan('users:purge-fake')
        ->expectsOutputToContain('real.person@gmail.com')
        ->assertSuccessful();
});

it('удаляет сгенерированные аккаунты вместе со связанными данными', function () {
    $fake = fakeAccount('bot3@example.com');

    $this->artisan('users:purge-fake --force')->assertSuccessful();

    // Без явной чистки удаление упало бы: у user_progress и user_words
    // внешний ключ объявлен как NO ACTION, база их сама не уносит.
    expect(User::find($fake->id))->toBeNull()
        ->and(DB::table('user_progress')->where('user_id', $fake->id)->count())->toBe(0)
        ->and(DB::table('user_words')->where('user_id', $fake->id)->count())->toBe(0);
});

it('не трогает настоящих пользователей и их данные', function () {
    fakeAccount('bot4@example.com');
    $real = fakeAccount('real.keeper@gmail.com');

    $this->artisan('users:purge-fake --force')->assertSuccessful();

    expect(User::find($real->id))->not->toBeNull()
        ->and(DB::table('user_progress')->where('user_id', $real->id)->count())->toBe(1)
        ->and(DB::table('user_words')->where('user_id', $real->id)->count())->toBe(1);
});

it('отказывается работать, если под маску попал администратор', function () {
    $admin = User::factory()->create([
        'email' => 'admin@example.com',
        'role_id' => Role::factory()->admin()->create()->id,
    ]);
    fakeAccount('bot5@example.com');

    // Защита от опечатки в --domain: сотрудников удалять нельзя даже
    // случайно, и в этом случае не удаляется вообще ничего.
    $this->artisan('users:purge-fake --force')->assertFailed();

    expect(User::find($admin->id))->not->toBeNull()
        ->and(User::where('email', 'bot5@example.com')->exists())->toBeTrue();
});

it('принимает другой домен параметром', function () {
    fakeAccount('bot6@test-domain.local');
    $keep = fakeAccount('bot7@example.com');

    $this->artisan('users:purge-fake --domain=test-domain.local --force')->assertSuccessful();

    expect(User::where('email', 'bot6@test-domain.local')->exists())->toBeFalse()
        ->and(User::find($keep->id))->not->toBeNull();
});

it('сообщает, когда удалять нечего', function () {
    User::factory()->create(['email' => 'only.real@gmail.com']);

    $this->artisan('users:purge-fake --domain=nothing-here.local')
        ->expectsOutputToContain('удалять нечего')
        ->assertSuccessful();
});
