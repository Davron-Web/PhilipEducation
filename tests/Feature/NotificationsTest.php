<?php

use App\Models\Billing\Payment;
use App\Models\Billing\Plan;
use App\Models\Billing\Subscription;
use App\Models\Content\Lesson;
use App\Models\Gamification\Achievement;
use App\Models\Gamification\Title;
use App\Models\System\Notification;
use App\Models\User;
use App\Models\User\Role;
use App\Notifications\SubscriptionExpiring;
use App\Services\AchievementService;
use App\Services\Payment\SubscriptionService;
use App\Services\XpService;
use Illuminate\Support\Facades\Mail;

it('сообщает о новом достижении', function () {
    $user = User::factory()->create(['points' => 0]);
    Achievement::create([
        'title' => 'Первое слово',
        'description' => 'Выучено первое слово',
        'points' => 10,
        'condition_type' => 'words_learned',
        'condition_value' => 1,
    ]);

    $word = App\Models\Vocabulary\Word::factory()->create();
    $user->words()->attach($word->id, ['learned' => true]);

    app(AchievementService::class)->checkAndAward($user, 'words_learned');

    expect($user->notifications()->where('title', 'like', 'Новое достижение%')->exists())->toBeTrue();
});

it('сообщает о новом звании', function () {
    Title::create(['code' => 'student', 'name' => 'Ученик', 'min_xp' => 0, 'is_active' => true]);

    $user = User::factory()->create(['points' => 0]);
    app(XpService::class)->award($user, 'lesson', 1);

    expect($user->notifications()->where('title', 'Новое звание: Ученик')->exists())->toBeTrue();
});

it('подтверждает оплату уведомлением и письмом', function () {
    Mail::fake();

    $user = User::factory()->create();
    $plan = Plan::create([
        'code' => 'notif-test', 'name' => 'Месяц', 'duration_days' => 30,
        'price_minor' => 7900, 'currency' => 'TJS', 'is_active' => true, 'sort_order' => 1,
    ]);

    $payment = app(SubscriptionService::class)->startCheckout($user, $plan)['payment'];
    app(SubscriptionService::class)->markPaid($payment, 'prov-1');

    $notification = $user->notifications()->where('title', 'Оплата прошла')->first();

    expect($notification)->not->toBeNull()
        ->and($notification->message)->toContain('79,00 TJS');
});

it('предупреждает о конце подписки за 7 и за 1 день', function () {
    $soon = User::factory()->create();
    $later = User::factory()->create();

    foreach ([[$soon, 1], [$later, 7]] as [$user, $days]) {
        Subscription::create([
            'user_id' => $user->id,
            'status' => Subscription::STATUS_ACTIVE,
            'starts_at' => now()->subDays(20),
            'ends_at' => now()->addDays($days),
        ]);
    }

    // Этого предупреждать рано — до конца ещё три дня, а команда шлёт
    // строго за 7 и за 1, иначе одно и то же ушло бы семь раз подряд.
    $notYet = User::factory()->create();
    Subscription::create([
        'user_id' => $notYet->id,
        'status' => Subscription::STATUS_ACTIVE,
        'starts_at' => now()->subDays(20),
        'ends_at' => now()->addDays(3),
    ]);

    $this->artisan('subscriptions:notify-expiring')->assertSuccessful();

    expect($soon->notifications()->count())->toBe(1)
        ->and($later->notifications()->count())->toBe(1)
        ->and($notYet->notifications()->count())->toBe(0);
});

it('не предупреждает о бессрочной подписке', function () {
    $user = User::factory()->create();
    Subscription::create([
        'user_id' => $user->id,
        'status' => Subscription::STATUS_ACTIVE,
        'source' => 'gift',
        'starts_at' => now()->subDays(20),
        'ends_at' => null,
    ]);

    $this->artisan('subscriptions:notify-expiring')->assertSuccessful();

    expect($user->notifications()->count())->toBe(0);
});

it('напоминает об учёбе тем, кто давно не заходил', function () {
    $away = User::factory()->create(['last_login_at' => now()->subDays(20)]);
    $active = User::factory()->create(['last_login_at' => now()->subDay()]);

    $this->artisan('students:remind')->assertSuccessful();

    expect($away->notifications()->count())->toBe(1)
        ->and($active->notifications()->count())->toBe(0);
});

it('не напоминает повторно в течение двух недель', function () {
    $user = User::factory()->create(['last_login_at' => now()->subDays(20)]);

    $this->artisan('students:remind');
    $this->artisan('students:remind');

    // Иначе для ушедших навсегда это стало бы еженедельным спамом.
    expect($user->notifications()->count())->toBe(1);
});

it('поздравляет с завершением уровня', function () {
    $user = User::factory()->create(['role_id' => Role::factory()->student()->create()->id]);
    $level = App\Models\System\Level::factory()->create(['name' => 'Beginner A1']);

    $lessons = Lesson::factory()->count(2)->create([
        'is_published' => true,
        'level_id' => $level->id,
    ]);

    $this->actingAs($user)->post("/public/lessons/{$lessons[0]->id}/complete");
    expect($user->notifications()->where('title', 'like', 'Уровень%')->count())->toBe(0);

    $this->actingAs($user)->post("/public/lessons/{$lessons[1]->id}/complete");
    expect($user->notifications()->where('title', 'Уровень Beginner A1 пройден')->count())->toBe(1);
});
