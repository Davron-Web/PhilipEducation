<?php

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

it('загружает аватар и сохраняет путь к файлу', function () {
    Storage::fake('public');
    $user = User::factory()->create(['email' => 'me.profile@gmail.com']);

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

it('меняет адрес и не запирает пользователя', function () {
    $user = User::factory()->create([
        'email' => 'old.address@gmail.com',
        'email_verified_at' => now(),
    ]);

    $this->actingAs($user)->put('/profiles', [
        'name' => $user->name,
        'email' => 'new.address@gmail.com',
    ]);

    $user->refresh();

    // Подтверждение почты отключено, поэтому смена адреса больше не
    // сбрасывает email_verified_at: сбросить его сейчас значило бы
    // отправить человека на страницу, которой нет.
    expect($user->email)->toBe('new.address@gmail.com')
        ->and($user->email_verified_at)->not->toBeNull();

    $this->actingAs($user)->get('/lessons')->assertOk();
});

it('не сбрасывает подтверждение, когда почта не менялась', function () {
    $user = User::factory()->create([
        'email' => 'same.profile@gmail.com',
        'email_verified_at' => now(),
    ]);

    $this->actingAs($user)->put('/profiles', [
        'name' => 'Новое имя',
        'email' => 'same.profile@gmail.com',
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
