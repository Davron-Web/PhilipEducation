<?php

use App\Models\Billing\Invoice;
use App\Models\Billing\Payment;
use App\Models\Billing\Plan;
use App\Models\Billing\Subscription;
use App\Models\User;
use App\Models\User\Role;
use App\Services\Payment\SubscriptionService;

function adminUser(): User
{
    return User::factory()->create(['role_id' => Role::factory()->admin()->create()->id]);
}

function testPlan(): Plan
{
    return Plan::create([
        'code' => 'billing-test',
        'name' => 'Тестовый месяц',
        'duration_days' => 30,
        'price_minor' => 7900,
        'currency' => 'TJS',
        'is_active' => true,
        'sort_order' => 1,
    ]);
}

it('выписывает счёт при успешной оплате', function () {
    $user = User::factory()->create();
    $plan = testPlan();

    $payment = app(SubscriptionService::class)->startCheckout($user, $plan)['payment'];

    app(SubscriptionService::class)->markPaid($payment, 'prov-1');

    $invoice = Invoice::where('payment_id', $payment->id)->first();

    expect($invoice)->not->toBeNull()
        ->and($invoice->amount_minor)->toBe(7900)
        ->and($invoice->plan_name)->toBe('Тестовый месяц')
        ->and($invoice->status)->toBe(Invoice::STATUS_PAID);
});

it('не выписывает второй счёт при повторном вебхуке', function () {
    $user = User::factory()->create();
    $plan = testPlan();

    $payment = app(SubscriptionService::class)->startCheckout($user, $plan)['payment'];

    // Провайдеры регулярно повторяют вебхук — двойной счёт удвоил бы доход.
    app(SubscriptionService::class)->markPaid($payment, 'prov-1');
    app(SubscriptionService::class)->markPaid($payment->fresh(), 'prov-1');

    expect(Invoice::count())->toBe(1);
});

it('отмечает истёкшие подписки командой', function () {
    $user = User::factory()->create();

    $expired = Subscription::create([
        'user_id' => $user->id,
        'plan_id' => testPlan()->id,
        'status' => Subscription::STATUS_ACTIVE,
        'starts_at' => now()->subDays(60),
        'ends_at' => now()->subDay(),
    ]);

    $lifetime = Subscription::create([
        'user_id' => User::factory()->create()->id,
        'status' => Subscription::STATUS_ACTIVE,
        'source' => 'gift',
        'starts_at' => now()->subDays(10),
        'ends_at' => null,
    ]);

    $this->artisan('subscriptions:expire')->assertSuccessful();

    expect($expired->fresh()->status)->toBe(Subscription::STATUS_EXPIRED)
        // Бессрочный подарок сроком не ограничен и истечь не может.
        ->and($lifetime->fresh()->status)->toBe(Subscription::STATUS_ACTIVE);
});

it('пишет время последнего входа', function () {
    $user = User::factory()->create([
        'password' => bcrypt('secret-password'),
        'last_login_at' => null,
    ]);

    $this->post('/login', ['email' => $user->email, 'password' => 'secret-password']);

    // Без этого «активных за 30 дней» в админке всегда показывало ноль.
    expect($user->fresh()->last_login_at)->not->toBeNull();
});

it('открывает админу разделы биллинга', function () {
    $admin = adminUser();

    foreach (['plans', 'subscriptions', 'payments', 'invoices'] as $section) {
        $this->actingAs($admin)->get("/admin/billing/{$section}")->assertOk();
    }
});

it('не пускает ученика в биллинг админки', function () {
    $student = User::factory()->create(['role_id' => Role::factory()->student()->create()->id]);

    $this->actingAs($student)->get('/admin/billing/plans')->assertForbidden();
    $this->actingAs($student)->get('/admin/billing/invoices')->assertForbidden();
});

it('позволяет админу создать тариф с ценой в сомони', function () {
    $this->actingAs(adminUser())
        ->post('/admin/billing/plans', [
            'code' => 'weekly-test',
            'name' => 'Неделя',
            'duration_days' => 7,
            'price' => 25.50,
            'currency' => 'TJS',
            'sort_order' => 0,
            'is_active' => 1,
        ])
        ->assertRedirect();

    // В форме сомони, в базе дирамы.
    expect(Plan::where('code', 'weekly-test')->value('price_minor'))->toBe(2550);
});

it('не даёт удалить тариф, по которому есть подписки', function () {
    $plan = testPlan();
    Subscription::create([
        'user_id' => User::factory()->create()->id,
        'plan_id' => $plan->id,
        'status' => Subscription::STATUS_ACTIVE,
        'starts_at' => now(),
        'ends_at' => now()->addDays(30),
    ]);

    $this->actingAs(adminUser())
        ->delete("/admin/billing/plans/{$plan->id}")
        ->assertRedirect();

    // Иначе платежи и счета потеряли бы тариф, к которому относятся.
    expect(Plan::find($plan->id))->not->toBeNull();
});
