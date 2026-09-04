<?php

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

it('загружает аватар и сохраняет путь к файлу', function () {
    Storage::fake('public');
    $user = User::factory()->create(['email' => 'me@example.com']);

    $this->actingAs($user)
        ->put('/profiles', [
            'name' => $user->name,
            'email' => $user->email,
            'avatar' => UploadedFile::fake()->image('me.jpg', 300, 300),
        ])
        ->assertRedirect();

    $user->refresh();

    expect($user->avatar)->not->toBeNull();
    Storage::disk('public')->assertExists($user->avatar);
});

it('отклоняет файл, который не является картинкой', function () {
    Storage::fake('public');
    $user = User::factory()->create();

    $this->actingAs($user)
        ->put('/profiles', [
            'name' => $user->name,
            'email' => $user->email,
            'avatar' => UploadedFile::fake()->create('payload.php', 10, 'text/plain'),
        ])
        ->assertSessionHasErrors('avatar');

    expect($user->fresh()->avatar)->toBeNull();
});

it('удаляет прежний файл при замене аватара', function () {
    Storage::fake('public');
    $user = User::factory()->create();

    $this->actingAs($user)->put('/profiles', [
        'name' => $user->name,
        'email' => $user->email,
        'avatar' => UploadedFile::fake()->image('first.jpg'),
    ]);

    $first = $user->fresh()->avatar;

    $this->actingAs($user)->put('/profiles', [
        'name' => $user->name,
        'email' => $user->email,
        'avatar' => UploadedFile::fake()->image('second.jpg'),
    ]);

    // Иначе storage постепенно забивается осиротевшими файлами.
    Storage::disk('public')->assertMissing($first);
    Storage::disk('public')->assertExists($user->fresh()->avatar);
});

it('убирает аватар по галочке', function () {
    Storage::fake('public');
    $user = User::factory()->create();

    $this->actingAs($user)->put('/profiles', [
        'name' => $user->name,
        'email' => $user->email,
        'avatar' => UploadedFile::fake()->image('me.jpg'),
    ]);

    $path = $user->fresh()->avatar;

    $this->actingAs($user)->put('/profiles', [
        'name' => $user->name,
        'email' => $user->email,
        'remove_avatar' => 1,
    ]);

    expect($user->fresh()->avatar)->toBeNull();
    Storage::disk('public')->assertMissing($path);
});

it('сбрасывает подтверждение при смене почты', function () {
    $user = User::factory()->create([
        'email' => 'old@example.com',
        'email_verified_at' => now(),
    ]);

    $this->actingAs($user)->put('/profiles', [
        'name' => $user->name,
        'email' => 'new@example.com',
    ]);

    $user->refresh();

    // Иначе чужой адрес оставался бы «подтверждённым» и годился
    // для восстановления пароля.
    expect($user->email)->toBe('new@example.com')
        ->and($user->email_verified_at)->toBeNull();
});

it('не сбрасывает подтверждение, когда почта не менялась', function () {
    $user = User::factory()->create([
        'email' => 'same@example.com',
        'email_verified_at' => now(),
    ]);

    $this->actingAs($user)->put('/profiles', [
        'name' => 'Новое имя',
        'email' => 'same@example.com',
    ]);

    expect($user->fresh()->email_verified_at)->not->toBeNull();
});

it('удаляет аккаунт после подтверждения паролем', function () {
    $user = User::factory()->create(['password' => bcrypt('secret-password')]);

    $this->actingAs($user)
        ->delete('/profiles', ['password' => 'secret-password'])
        ->assertRedirect('/');

    $this->assertGuest();
    expect(User::find($user->id))->toBeNull();
});

it('не удаляет аккаунт при неверном пароле', function () {
    $user = User::factory()->create(['password' => bcrypt('secret-password')]);

    $this->actingAs($user)
        ->delete('/profiles', ['password' => 'wrong'])
        ->assertSessionHasErrors('password', errorBag: 'userDeletion');

    expect(User::find($user->id))->not->toBeNull();
});
