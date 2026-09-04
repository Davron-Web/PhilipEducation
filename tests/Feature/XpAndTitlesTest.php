<?php

use App\Models\Content\Lesson;
use App\Models\Gamification\Achievement;
use App\Models\Gamification\Title;
use App\Models\User;
use App\Models\User\Role;
use App\Services\AchievementService;
use App\Services\XpService;

function titled(int $minXp, string $code): Title
{
    return Title::create([
        'code' => $code,
        'name' => 'Звание '.$code,
        'min_xp' => $minXp,
        'is_active' => true,
    ]);
}

it('начисляет опыт и записывает его в журнал', function () {
    $user = User::factory()->create(['points' => 0]);

    $awarded = app(XpService::class)->award($user, 'lesson', 7);

    expect($awarded)->toBe(25)
        ->and($user->fresh()->points)->toBe(25)
        ->and($user->xpEvents()->count())->toBe(1);
});

it('не начисляет опыт дважды за одно действие', function () {
    $user = User::factory()->create(['points' => 0]);
    $xp = app(XpService::class);

    $xp->award($user->fresh(), 'lesson', 7);
    $second = $xp->award($user->fresh(), 'lesson', 7);

    // Пересдача теста или повторный вход в урок не должны накручивать опыт.
    expect($second)->toBe(0)
        ->and($user->fresh()->points)->toBe(25)
        ->and($user->xpEvents()->count())->toBe(1);
});

it('начисляет опыт отдельно за разные уроки', function () {
    $user = User::factory()->create(['points' => 0]);
    $xp = app(XpService::class);

    $xp->award($user->fresh(), 'lesson', 1);
    $xp->award($user->fresh(), 'lesson', 2);

    expect($user->fresh()->points)->toBe(50);
});

it('выдаёт титул при достижении порога', function () {
    titled(0, 'novice');
    titled(50, 'student');
    titled(1000, 'master');

    $user = User::factory()->create(['points' => 0]);
    $xp = app(XpService::class);

    $xp->award($user->fresh(), 'lesson', 1);
    $xp->award($user->fresh(), 'lesson', 2);

    $user->refresh();

    // 50 XP: два нижних титула получены, верхний — нет.
    expect($user->titles()->count())->toBe(2)
        ->and($user->currentTitle()->code)->toBe('student');
});

it('не выдаёт отключённые титулы', function () {
    Title::create(['code' => 'hidden', 'name' => 'Скрытый', 'min_xp' => 0, 'is_active' => false]);

    $user = User::factory()->create(['points' => 0]);
    app(XpService::class)->award($user, 'lesson', 1);

    expect($user->fresh()->titles()->count())->toBe(0);
});

it('даёт опыт за пройденный урок и записывает потраченное время', function () {
    $user = User::factory()->create([
        'role_id' => Role::factory()->student()->create()->id,
        'points' => 0,
    ]);
    $lesson = Lesson::factory()->create(['is_published' => true]);

    $this->actingAs($user)
        ->post("/public/lessons/{$lesson->id}/complete", ['seconds_spent' => 600])
        ->assertRedirect();

    $progress = $user->progress()->where('lesson_id', $lesson->id)->first();

    expect($user->fresh()->points)->toBe(25)
        ->and($progress->time_spent)->toBe(600)
        ->and($progress->is_completed)->toBeTrue();
});

it('обрезает завышенное время на уроке', function () {
    $user = User::factory()->create(['role_id' => Role::factory()->student()->create()->id]);
    $lesson = Lesson::factory()->create(['is_published' => true]);

    // Значение приходит из браузера, поэтому подделать его несложно.
    $this->actingAs($user)
        ->post("/public/lessons/{$lesson->id}/complete", ['seconds_spent' => 999999]);

    expect($user->progress()->where('lesson_id', $lesson->id)->value('time_spent'))
        ->toBe(4 * 3600);
});

it('начисляет опыт за полученное достижение', function () {
    $user = User::factory()->create(['points' => 0]);
    $achievement = Achievement::create([
        'title' => 'Первое слово',
        'description' => 'test',
        'points' => 30,
        'condition_type' => 'words_learned',
        'condition_value' => 1,
    ]);

    $word = App\Models\Vocabulary\Word::factory()->create();
    $user->words()->attach($word->id, ['learned' => true]);

    app(AchievementService::class)->checkAndAward($user, 'words_learned');

    // Колонка points у достижения теперь действительно что-то значит.
    expect($user->fresh()->points)->toBe(30)
        ->and($user->achievements()->count())->toBe(1);
});
