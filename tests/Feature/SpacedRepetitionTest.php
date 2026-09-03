<?php

use App\Models\User;
use App\Models\User\UserWord;
use App\Models\Vocabulary\Word;
use App\Services\SpacedRepetitionService;

function progressFor(User $user): UserWord
{
    $word = Word::factory()->create();

    return UserWord::create(['user_id' => $user->id, 'word_id' => $word->id]);
}

it('растягивает интервал при верных ответах', function () {
    $srs = app(SpacedRepetitionService::class);
    $progress = progressFor(User::factory()->create());

    $srs->review($progress, true);
    expect($progress->interval_days)->toBe(1);

    $srs->review($progress, true);
    expect($progress->interval_days)->toBe(3);

    $srs->review($progress, true);
    // Третий интервал считается уже через коэффициент лёгкости.
    expect($progress->interval_days)->toBeGreaterThan(3);
});

it('сбрасывает цепочку и возвращает слово в ту же сессию при ошибке', function () {
    $srs = app(SpacedRepetitionService::class);
    $progress = progressFor(User::factory()->create());

    $srs->review($progress, true);
    $srs->review($progress, true);
    expect($progress->repetitions)->toBe(2);

    $srs->review($progress, false);

    expect($progress->repetitions)->toBe(0)
        ->and($progress->interval_days)->toBe(0)
        ->and($progress->next_review_at->isToday())->toBeTrue();
});

it('считает слово выученным только после трёх удачных повторений', function () {
    $srs = app(SpacedRepetitionService::class);
    $progress = progressFor(User::factory()->create());

    $srs->review($progress, true);
    expect($progress->learned)->toBeFalse();

    $srs->review($progress, true);
    expect($progress->learned)->toBeFalse();

    $srs->review($progress, true);
    expect($progress->learned)->toBeTrue();
});

it('снимает отметку «выучено», если слово забыли', function () {
    $srs = app(SpacedRepetitionService::class);
    $progress = progressFor(User::factory()->create());

    $srs->review($progress, true);
    $srs->review($progress, true);
    $srs->review($progress, true);
    expect($progress->learned)->toBeTrue();

    $srs->review($progress, false);
    expect($progress->learned)->toBeFalse();
});

it('снижает коэффициент лёгкости у трудных слов и не опускает ниже минимума', function () {
    $srs = app(SpacedRepetitionService::class);
    $progress = progressFor(User::factory()->create());

    $easeBefore = $progress->ease_factor ?: 2.5;
    $srs->review($progress, false);
    expect($progress->ease_factor)->toBeLessThan($easeBefore);

    for ($i = 0; $i < 20; $i++) {
        $srs->review($progress, false);
    }

    expect($progress->ease_factor)->toBeGreaterThanOrEqual(1.3);
});

it('в очередь попадают только слова, у которых подошёл срок', function () {
    $srs = app(SpacedRepetitionService::class);
    $user = User::factory()->create();

    $due = progressFor($user);
    $due->update(['next_review_at' => now()->subDay()]);

    $later = progressFor($user);
    $later->update(['next_review_at' => now()->addDays(3)]);

    $never = progressFor($user); // не запланировано вовсе

    $queue = $srs->dueQueue($user);

    expect($srs->dueCount($user))->toBe(1)
        ->and($queue->pluck('word_id')->all())->toBe([$due->word_id]);
});

it('не отдаёт в очередь чужие слова', function () {
    $srs = app(SpacedRepetitionService::class);
    $mine = User::factory()->create();
    $other = User::factory()->create();

    progressFor($other)->update(['next_review_at' => now()->subDay()]);

    expect($srs->dueCount($mine))->toBe(0);
});

it('планирует первое повторение при отметке «знаю», не сбрасывая уже накопленный интервал', function () {
    $srs = app(SpacedRepetitionService::class);
    $user = User::factory()->create();
    $word = Word::factory()->create();

    $this->actingAs($user)
        ->postJson(route('words.progress', $word), ['learned' => true])
        ->assertOk();

    $progress = UserWord::where('user_id', $user->id)->where('word_id', $word->id)->first();
    expect($progress->next_review_at)->not->toBeNull();

    // Уводим срок далеко вперёд и повторяем отметку — она не должна его сбить.
    $progress->update(['next_review_at' => now()->addDays(30), 'interval_days' => 30]);

    $this->actingAs($user)->postJson(route('words.progress', $word), ['learned' => true]);

    expect($progress->fresh()->interval_days)->toBe(30);
});

it('сессия повторения открывается и принимает ответ', function () {
    $user = User::factory()->create();
    $progress = progressFor($user);
    $progress->update(['next_review_at' => now()->subDay()]);

    $this->actingAs($user)->get(route('words.review'))->assertOk();

    $this->actingAs($user)
        ->postJson(route('words.review.answer', $progress->word_id), ['remembered' => true])
        ->assertOk()
        ->assertJsonPath('ok', true);

    expect($progress->fresh()->next_review_at->isFuture())->toBeTrue();
});
