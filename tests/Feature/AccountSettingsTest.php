<?php

use App\Models\User;
use Illuminate\Support\Facades\Storage;

it('выбирает аватар из готового набора', function () {
    $user = User::factory()->create(['avatar' => null]);
    $chosen = config('avatars.files')[2];

    $this->actingAs($user)
        ->patch('/profiles/avatar', ['avatar' => $chosen])
        ->assertRedirect();

    expect($user->fresh()->avatar)->toBe($chosen);
});

it('отклоняет аватар, которого нет в наборе', function () {
    $user = User::factory()->create(['avatar' => null]);

    $this->actingAs($user)
        ->patch('/profiles/avatar', ['avatar' => 'not-a-real-file.png'])
        ->assertSessionHasErrors('avatar');

    expect($user->fresh()->avatar)->toBeNull();
});

it('позволяет сменить выбранный аватар на другой из набора', function () {
    $user = User::factory()->create(['avatar' => config('avatars.files')[0]]);
    $next = config('avatars.files')[1];

    $this->actingAs($user)->patch('/profiles/avatar', ['avatar' => $next]);

    expect($user->fresh()->avatar)->toBe($next);
});

it('подчищает старый загруженный вручную файл при выборе аватара из набора', function () {
    // Имитация данных ДО того, как загрузку файла убрали в пользу галереи —
    // такие пути ещё могут встречаться у части пользователей.
    Storage::fake('public');
    Storage::disk('public')->put('avatars/legacy-upload.jpg', 'fake image bytes');
    $user = User::factory()->create(['avatar' => 'avatars/legacy-upload.jpg']);

    $this->actingAs($user)->patch('/profiles/avatar', ['avatar' => config('avatars.files')[0]]);

    Storage::disk('public')->assertMissing('avatars/legacy-upload.jpg');
    expect($user->fresh()->avatar)->toBe(config('avatars.files')[0]);
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
