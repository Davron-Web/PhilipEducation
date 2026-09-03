<?php

use App\Models\Billing\Subscription;
use App\Models\User\Role;
use App\Models\User;
use App\Services\Payment\SubscriptionService;

function admin(): User
{
    return User::factory()->create(['role_id' => Role::factory()->admin()->create()->id]);
}

it('открывает платные разделы после подарка от админа', function () {
    $user = User::factory()->create();

    expect($user->hasActiveSubscription())->toBeFalse();

    app(SubscriptionService::class)->grant($user, 30, admin(), 'победитель конкурса');

    expect($user->fresh()->hasActiveSubscription())->toBeTrue();

    $this->actingAs($user->fresh())->get('/books')->assertOk();
});

it('выдаёт бессрочный доступ, когда срок не указан', function () {
    $user = User::factory()->create();

    $subscription = app(SubscriptionService::class)->grant($user, null, admin());

    expect($subscription->isLifetime())->toBeTrue()
        ->and($subscription->ends_at)->toBeNull()
        ->and($user->fresh()->hasActiveSubscription())->toBeTrue();
});

it('помечает подарок и запоминает, кто его выдал', function () {
    $user = User::factory()->create();
    $granter = admin();

    $subscription = app(SubscriptionService::class)->grant($user, 7, $granter, 'тестовый доступ');

    expect($subscription->isGift())->toBeTrue()
        ->and($subscription->source)->toBe(Subscription::SOURCE_GIFT)
        ->and($subscription->granted_by)->toBe($granter->id)
        ->and($subscription->note)->toBe('тестовый доступ');
});

it('продлевает существующий доступ, а не заводит вторую подписку', function () {
    $user = User::factory()->create();
    $service = app(SubscriptionService::class);

    // Админа создаём один раз: фабрика ролей берёт имена из ограниченного
    // пула уникальных значений и на повторных вызовах исчерпывается.
    $granter = admin();
    $service->grant($user, 10, $granter);
    $service->grant($user, 10, $granter);

    expect($user->subscriptions()->count())->toBe(1)
        ->and($user->fresh()->activeSubscription()->daysLeft())->toBeGreaterThan(15);
});

it('отзыв подарка закрывает доступ сразу', function () {
    $user = User::factory()->create();
    $service = app(SubscriptionService::class);

    $subscription = $service->grant($user, 30, admin());
    $service->revoke($subscription);

    expect($user->fresh()->hasActiveSubscription())->toBeFalse();

    $this->actingAs($user->fresh())->get('/books')->assertRedirect(route('billing.plans'));
});

it('не даёт отозвать оплаченную подписку через админку', function () {
    $user = User::factory()->create();

    $user->subscriptions()->create([
        'status' => Subscription::STATUS_ACTIVE,
        'source' => Subscription::SOURCE_PAID,
        'starts_at' => now(),
        'ends_at' => now()->addDays(20),
    ]);

    $this->actingAs(admin())
        ->delete(route('admin.user.users.premium.revoke', $user))
        ->assertSessionHas('error');

    expect($user->fresh()->hasActiveSubscription())->toBeTrue();
});

it('оплата не укорачивает бессрочный подарок', function () {
    $user = User::factory()->create();
    $service = app(SubscriptionService::class);

    $service->grant($user, null, admin());

    $plan = App\Models\Billing\Plan::firstOrCreate(
        ['code' => 'gift-test-month'],
        ['name' => 'Месяц', 'duration_days' => 30, 'price_minor' => 1000, 'currency' => 'TJS', 'is_active' => true, 'sort_order' => 90],
    );

    $payment = App\Models\Billing\Payment::create([
        'user_id' => $user->id,
        'plan_id' => $plan->id,
        'amount_minor' => 1000,
        'currency' => 'TJS',
        'status' => App\Models\Billing\Payment::STATUS_PENDING,
        'provider' => 'sandbox',
        'reference' => 'gift-'.uniqid(),
    ]);

    $service->markPaid($payment);

    expect($user->fresh()->activeSubscription()->isLifetime())->toBeTrue();
});

it('админ выдаёт премиум через форму в карточке пользователя', function () {
    $user = User::factory()->create();

    $this->actingAs(admin())
        ->post(route('admin.user.users.premium.grant', $user), ['duration' => '30', 'note' => 'подарок'])
        ->assertSessionHas('success');

    expect($user->fresh()->hasActiveSubscription())->toBeTrue();
});
