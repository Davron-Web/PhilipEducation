<?php

use App\Models\Billing\Payment;
use App\Models\Billing\Plan;
use App\Models\Billing\Subscription;
use App\Models\User;
use App\Services\Payment\SubscriptionService;

beforeEach(function () {
    $this->plan = Plan::updateOrCreate(
        ['code' => 'test-monthly'],
        ['name' => 'Тест-месяц', 'duration_days' => 30, 'price_minor' => 7900, 'currency' => 'TJS', 'is_active' => true, 'sort_order' => 99]
    );
});

it('не пускает без подписки в платный раздел', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get('/books')
        ->assertRedirect(route('billing.plans'));
});

it('пускает в платный раздел после оплаты', function () {
    $user = User::factory()->create();
    $service = app(SubscriptionService::class);

    $payment = Payment::create([
        'user_id' => $user->id,
        'plan_id' => $this->plan->id,
        'amount_minor' => $this->plan->price_minor,
        'currency' => 'TJS',
        'status' => Payment::STATUS_PENDING,
        'provider' => 'sandbox',
        'reference' => 'test-'.uniqid(),
    ]);

    $service->markPaid($payment);

    $this->actingAs($user->fresh())
        ->get('/books')
        ->assertOk();
});

it('не выдаёт два периода за один платёж при повторном вебхуке', function () {
    $user = User::factory()->create();
    $service = app(SubscriptionService::class);

    $payment = Payment::create([
        'user_id' => $user->id,
        'plan_id' => $this->plan->id,
        'amount_minor' => $this->plan->price_minor,
        'currency' => 'TJS',
        'status' => Payment::STATUS_PENDING,
        'provider' => 'sandbox',
        'reference' => 'test-'.uniqid(),
    ]);

    $first = $service->markPaid($payment);
    $endsAt = $first->ends_at->copy();

    // Провайдеры регулярно дублируют вебхуки.
    $service->markPaid($payment->fresh());

    expect($user->subscriptions()->count())->toBe(1)
        ->and($first->fresh()->ends_at->timestamp)->toBe($endsAt->timestamp);
});

it('продлевает подписку от даты окончания, а не от сегодня', function () {
    $user = User::factory()->create();
    $service = app(SubscriptionService::class);

    $existing = $user->subscriptions()->create([
        'plan_id' => $this->plan->id,
        'status' => Subscription::STATUS_ACTIVE,
        'starts_at' => now()->subDays(5),
        'ends_at' => now()->addDays(10),
    ]);

    $payment = Payment::create([
        'user_id' => $user->id,
        'plan_id' => $this->plan->id,
        'amount_minor' => $this->plan->price_minor,
        'currency' => 'TJS',
        'status' => Payment::STATUS_PENDING,
        'provider' => 'sandbox',
        'reference' => 'test-'.uniqid(),
    ]);

    $service->markPaid($payment);

    // 10 оставшихся дней + 30 новых, а не 30 от сегодня.
    expect($existing->fresh()->ends_at->startOfDay()->toDateString())
        ->toBe(now()->addDays(40)->startOfDay()->toDateString());
});

it('после отмены доступ сохраняется до конца оплаченного периода', function () {
    $user = User::factory()->create();

    $subscription = $user->subscriptions()->create([
        'plan_id' => $this->plan->id,
        'status' => Subscription::STATUS_ACTIVE,
        'starts_at' => now(),
        'ends_at' => now()->addDays(10),
    ]);

    app(SubscriptionService::class)->cancel($subscription);

    expect($user->fresh()->hasActiveSubscription())->toBeTrue();

    $this->actingAs($user->fresh())->get('/books')->assertOk();
});

it('истёкшая подписка доступ не даёт', function () {
    $user = User::factory()->create();

    $user->subscriptions()->create([
        'plan_id' => $this->plan->id,
        'status' => Subscription::STATUS_ACTIVE,
        'starts_at' => now()->subDays(40),
        'ends_at' => now()->subDay(),
    ]);

    expect($user->fresh()->hasActiveSubscription())->toBeFalse();
});

it('бесплатные уровни доступны без подписки, платные — нет', function () {
    $user = User::factory()->create();

    expect($user->canAccessLevel('A1'))->toBeTrue()
        ->and($user->canAccessLevel('A2'))->toBeTrue()
        ->and($user->canAccessLevel('B1'))->toBeFalse()
        ->and($user->canAccessLevel(null))->toBeTrue();
});

it('страница тарифов открыта гостю', function () {
    $this->get('/plans')->assertOk();
});
