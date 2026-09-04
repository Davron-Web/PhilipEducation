<?php

use App\Models\Billing\Payment;
use App\Models\Billing\Plan;
use App\Models\System\Notification;
use App\Models\Test\TestAttempt;
use App\Models\User;
use App\Models\User\Role;

/**
 * Изоляция данных между пользователями: ученик не должен доставать чужое,
 * подставив другой id в адрес. Каждая проверка здесь — это дыра, которая
 * либо была открыта, либо закроется только политикой.
 */
function student(): User
{
    return User::factory()->create(['role_id' => Role::factory()->student()->create()->id]);
}

it('не отдаёт чужой профиль в HTML', function () {
    $me = student();
    $other = User::factory()->create();

    $this->actingAs($me)->get("/profiles/{$other->id}")->assertForbidden();
});

it('не отдаёт чужой профиль в JSON', function () {
    $me = student();
    $other = User::factory()->create();

    // Именно эта ветка утекала: email, история обучения, результаты тестов
    // и сертификаты любого пользователя по подставленному id.
    $this->actingAs($me)
        ->getJson("/profiles/{$other->id}")
        ->assertForbidden();
});

it('показывает пользователю его собственный профиль', function () {
    $me = student();

    $this->actingAs($me)->get("/profiles/{$me->id}")->assertOk();
});

it('позволяет администратору смотреть чужой профиль', function () {
    $admin = User::factory()->create(['role_id' => Role::factory()->admin()->create()->id]);
    $other = User::factory()->create();

    $this->actingAs($admin)->get("/profiles/{$other->id}")->assertOk();
});

it('не показывает чужую попытку теста', function () {
    $me = student();
    $attempt = TestAttempt::factory()->create();

    $this->actingAs($me)->get("/tests/attempts/{$attempt->id}")->assertForbidden();
});

it('показывает собственную попытку теста', function () {
    $me = student();
    $attempt = TestAttempt::factory()->forUser($me->id)->create();

    $this->actingAs($me)->get("/tests/attempts/{$attempt->id}")->assertOk();
});

it('не показывает чужое уведомление', function () {
    $me = student();
    $notification = Notification::factory()->create();

    // Здесь защита — не политика, а выборка по user_id, поэтому 404, а не 403:
    // о существовании чужого уведомления пользователь знать не должен.
    $this->actingAs($me)->get("/notifications/{$notification->id}")->assertNotFound();
});

it('не даёт оплатить чужой платёж в песочнице', function () {
    $me = student();
    $other = User::factory()->create();

    $plan = Plan::create([
        'code' => 'iso-test',
        'name' => 'Тестовый тариф',
        'duration_days' => 30,
        'price_minor' => 1000,
        'currency' => 'TJS',
        'is_active' => true,
        'sort_order' => 1,
    ]);
    $payment = Payment::create([
        'user_id' => $other->id,
        'plan_id' => $plan->id,
        'amount_minor' => $plan->price_minor,
        'currency' => $plan->currency,
        'status' => 'pending',
        'provider' => 'sandbox',
        'reference' => 'pe-'.str_repeat('a', 20),
    ]);

    $this->actingAs($me)
        ->post("/billing/sandbox/{$payment->reference}", ['success' => true])
        ->assertForbidden();

    expect($payment->fresh()->status)->toBe('pending');
});
