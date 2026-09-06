<?php

use App\Models\Content\Lesson;
use App\Models\System\Level;
use App\Models\Test\Test;
use App\Models\Test\TestAnswer;
use App\Models\Test\TestQuestion;
use App\Models\User;
use App\Models\User\Role;
use Laravel\Sanctum\Sanctum;

function apiStudent(): User
{
    return User::factory()->create([
        'role_id' => Role::factory()->student()->create()->id,
        'password' => bcrypt('secret-password'),
        'points' => 0,
        // Фабрика ставит is_active случайно (boolean(90)), а вход отключённым
        // аккаунтам закрыт — без этого тест падал примерно раз из десяти.
        'is_active' => true,
    ]);
}

it('выдаёт токен по верным данным', function () {
    $user = apiStudent();

    $this->postJson('/api/v1/auth/login', [
        'email' => $user->email,
        'password' => 'secret-password',
        'device_name' => 'iphone',
    ])
        ->assertOk()
        ->assertJsonStructure(['token', 'user' => ['id', 'name', 'email', 'xp']]);
});

it('не выдаёт токен по неверному паролю', function () {
    $user = apiStudent();

    $this->postJson('/api/v1/auth/login', [
        'email' => $user->email,
        'password' => 'wrong',
        'device_name' => 'iphone',
    ])->assertStatus(422);
});

it('не подсказывает, зарегистрирован ли email', function () {
    $user = apiStudent();

    $unknown = $this->postJson('/api/v1/auth/login', [
        'email' => 'nobody@example.com', 'password' => 'x', 'device_name' => 'd',
    ]);

    $wrongPassword = $this->postJson('/api/v1/auth/login', [
        'email' => $user->email, 'password' => 'x', 'device_name' => 'd',
    ]);

    // Разные тексты позволяли бы перебором собрать список зарегистрированных.
    expect($unknown->json('errors.email'))->toBe($wrongPassword->json('errors.email'));
});

it('не пускает в закрытые эндпоинты без токена', function () {
    foreach (['/api/v1/progress', '/api/v1/lessons', '/api/v1/auth/me', '/api/v1/subscription'] as $url) {
        $this->getJson($url)->assertUnauthorized();
    }
});

it('отдаёт тарифы без авторизации', function () {
    App\Models\Billing\Plan::create([
        'code' => 'api-month', 'name' => 'Месяц', 'duration_days' => 30,
        'price_minor' => 7900, 'currency' => 'TJS', 'is_active' => true, 'sort_order' => 1,
    ]);

    $this->getJson('/api/v1/plans')
        ->assertOk()
        // Цена в сомони: способ хранения в дирамах клиента не касается.
        ->assertJsonPath('data.0.price', 79);
});

it('возвращает профиль по токену', function () {
    $user = apiStudent();
    Sanctum::actingAs($user);

    $this->getJson('/api/v1/auth/me')
        ->assertOk()
        ->assertJsonPath('data.id', $user->id)
        ->assertJsonPath('data.email', $user->email);
});

it('не отдаёт лишние поля пользователя', function () {
    $user = apiStudent();
    Sanctum::actingAs($user);

    $data = $this->getJson('/api/v1/auth/me')->json('data');

    // Ресурс перечисляет поля явно, поэтому новая колонка не утечёт сама.
    expect($data)->not->toHaveKey('password')
        ->and($data)->not->toHaveKey('remember_token')
        ->and($data)->not->toHaveKey('role_id');
});

it('начисляет опыт за урок через API', function () {
    $user = apiStudent();
    Sanctum::actingAs($user);

    // Уровень A1 бесплатный — иначе урок закрыт подпиской (см. payment.free_levels).
    $level = Level::factory()->create(['code' => 'A1']);
    $lesson = Lesson::factory()->create(['is_published' => true, 'level_id' => $level->id]);

    $this->postJson("/api/v1/lessons/{$lesson->id}/complete", ['seconds_spent' => 300])
        ->assertOk()
        ->assertJsonPath('xp_awarded', 25)
        ->assertJsonPath('xp_total', 25);

    expect($user->progress()->where('lesson_id', $lesson->id)->value('time_spent'))->toBe(300);
});

it('закрывает платный уровень без подписки', function () {
    $user = apiStudent();
    Sanctum::actingAs($user);

    $level = Level::factory()->create(['code' => 'C1']);
    $lesson = Lesson::factory()->create(['is_published' => true, 'level_id' => $level->id]);

    $this->getJson("/api/v1/lessons/{$lesson->id}")->assertForbidden();

    giveSubscription($user);

    $this->getJson("/api/v1/lessons/{$lesson->id}")->assertOk();
});

it('не отдаёт правильные ответы в вопросах теста', function () {
    $user = apiStudent();
    Sanctum::actingAs($user);

    $test = Test::factory()->create();
    $question = TestQuestion::factory()->singleChoice()->create(['test_id' => $test->id]);
    TestAnswer::factory()->create(['question_id' => $question->id, 'is_correct' => true]);
    TestAnswer::factory()->create(['question_id' => $question->id, 'is_correct' => false]);

    $options = $this->getJson("/api/v1/tests/{$test->id}")->json('data.questions.0.options');

    // Иначе клиент получал бы ключ к тесту вместе с самим тестом.
    foreach ($options as $option) {
        expect($option)->not->toHaveKey('is_correct');
    }
});

it('удаляет только текущий токен при выходе', function () {
    $user = apiStudent();

    $phone = $user->createToken('phone')->plainTextToken;
    $user->createToken('tablet');

    $this->withHeader('Authorization', 'Bearer '.$phone)
        ->postJson('/api/v1/auth/logout')
        ->assertOk();

    // Выход на телефоне не должен разлогинивать планшет.
    expect($user->tokens()->count())->toBe(1)
        ->and($user->tokens()->first()->name)->toBe('tablet');
});
