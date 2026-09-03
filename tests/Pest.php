<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/*
|--------------------------------------------------------------------------
| tests Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "pest()" function to bind a different classes or traits.
|
*/

pest()->extend(TestCase::class)
    ->use(RefreshDatabase::class)
    ->in('Feature');

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain conditions. The
| "expect()" function gives you access to a set of "expectations" methods that you can use
| to assert different things. Of course, you may extend the Expectation API at any time.
|
*/

expect()->extend('toBeOne', function () {
    return $this->toBe(1);
});

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| While Pest is very powerful out-of-the-box, you may have some testing code specific to your
| project that you don't want to repeat in every file. Here you can also expose helpers as
| global functions to help you to reduce the number of lines of code in your test files.
|
*/

/**
 * Выдаёт пользователю активную подписку — нужно тестам разделов,
 * закрытых платным доступом (книги, IELTS, Phil).
 */
function giveSubscription(App\Models\User $user, int $days = 30): App\Models\Billing\Subscription
{
    $plan = App\Models\Billing\Plan::firstOrCreate(
        ['code' => 'test-plan'],
        [
            'name' => 'Тестовый тариф',
            'duration_days' => 30,
            'price_minor' => 1000,
            'currency' => 'TJS',
            'is_active' => true,
            'sort_order' => 99,
        ],
    );

    return $user->subscriptions()->create([
        'plan_id' => $plan->id,
        'status' => App\Models\Billing\Subscription::STATUS_ACTIVE,
        'starts_at' => now(),
        'ends_at' => now()->addDays($days),
    ]);
}
