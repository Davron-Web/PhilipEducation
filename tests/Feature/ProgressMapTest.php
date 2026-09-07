<?php

use App\Models\Content\Lesson;
use App\Models\System\Level;
use App\Models\Test\Test;
use App\Models\Test\TestAnswer;
use App\Models\Test\TestQuestion;
use App\Models\User;
use App\Models\User\Role;
use App\Services\ProgressMapService;
use App\Services\TestGradingService;
use Illuminate\Support\Facades\DB;

function mapStudent(): User
{
    return User::factory()->create([
        'role_id' => Role::factory()->student()->create()->id,
        'is_active' => true,
    ]);
}

function answerMapTopic(User $user, string $topic, int $count, int $correct): void
{
    $test = Test::factory()->create(['passing_score' => 50]);
    $answers = [];

    for ($i = 0; $i < $count; $i++) {
        $question = TestQuestion::factory()->singleChoice()->create(['test_id' => $test->id, 'topic' => $topic]);
        $right = TestAnswer::create(['question_id' => $question->id, 'answer' => 'r', 'is_correct' => true, 'sort_order' => 0]);
        $wrong = TestAnswer::create(['question_id' => $question->id, 'answer' => 'w', 'is_correct' => false, 'sort_order' => 1]);
        $answers[$question->id] = (string) ($i < $correct ? $right->id : $wrong->id);
    }

    app(TestGradingService::class)->grade($user, $test->fresh(), $answers);
}

it('не пускает гостя на карту прогресса', function () {
    $this->get('/progress')->assertRedirect('/login');
});

it('считает долю пройденных уроков по уровням', function () {
    $user = mapStudent();
    $level = Level::factory()->create(['code' => 'A1']);
    $lessons = Lesson::factory()->count(4)->create(['is_published' => true, 'level_id' => $level->id]);

    $user->progress()->create(['lesson_id' => $lessons[0]->id, 'is_completed' => true, 'progress_percent' => 100]);
    $user->progress()->create(['lesson_id' => $lessons[1]->id, 'is_completed' => false, 'progress_percent' => 40]);

    $row = app(ProgressMapService::class)->levels($user)->firstWhere('level.code', 'A1');

    expect($row['total'])->toBe(4)
        ->and($row['completed'])->toBe(1)
        ->and($row['in_progress'])->toBe(1)
        ->and($row['percent'])->toBe(25);
});

it('не учитывает неопубликованные уроки', function () {
    $user = mapStudent();
    $level = Level::factory()->create(['code' => 'A2']);
    Lesson::factory()->count(2)->create(['is_published' => true, 'level_id' => $level->id]);
    Lesson::factory()->create(['is_published' => false, 'level_id' => $level->id]);

    expect(app(ProgressMapService::class)->levels($user)->firstWhere('level.code', 'A2')['total'])->toBe(2);
});

it('не считает чужой прогресс', function () {
    $mine = mapStudent();
    $other = mapStudent();
    $level = Level::factory()->create(['code' => 'B1']);
    $lessons = Lesson::factory()->count(2)->create(['is_published' => true, 'level_id' => $level->id]);

    $other->progress()->create(['lesson_id' => $lessons[0]->id, 'is_completed' => true, 'progress_percent' => 100]);

    expect(app(ProgressMapService::class)->levels($mine)->firstWhere('level.code', 'B1')['completed'])->toBe(0);
});

it('разделяет слабые и уверенные темы', function () {
    $user = mapStudent();
    answerMapTopic($user, 'passive-voice', 5, 1);
    answerMapTopic($user, 'articles', 5, 5);

    $map = app(ProgressMapService::class);

    expect($map->weakTopics($user)->pluck('topic')->all())->toBe(['passive-voice'])
        ->and($map->strongTopics($user)->pluck('topic')->all())->toBe(['articles']);
});

it('показывает карту с уровнями и темами', function () {
    $user = mapStudent();
    $level = Level::factory()->create(['code' => 'A1', 'name' => 'Beginner']);
    Lesson::factory()->create(['is_published' => true, 'level_id' => $level->id]);
    answerMapTopic($user, 'passive-voice', 4, 0);

    $this->actingAs($user)
        ->get('/progress')
        ->assertOk()
        ->assertSee('Beginner')
        ->assertSee('Passive Voice')
        ->assertSee('Стоит повторить');
});

it('честно говорит, когда судить о темах рано', function () {
    $user = mapStudent();
    Lesson::factory()->create(['is_published' => true, 'level_id' => Level::factory()->create()->id]);

    $this->actingAs($user)
        ->get('/progress')
        ->assertOk()
        ->assertSee('Про темы пока сказать нечего');
});

it('не делает запрос на каждый уровень', function () {
    $user = mapStudent();

    foreach (['A1', 'A2', 'B1', 'B2', 'C1'] as $code) {
        $level = Level::factory()->create(['code' => $code]);
        Lesson::factory()->count(5)->create(['is_published' => true, 'level_id' => $level->id]);
    }

    DB::flushQueryLog();
    DB::enableQueryLog();
    app(ProgressMapService::class)->levels($user);
    $count = count(DB::getQueryLog());
    DB::disableQueryLog();

    // Три запроса: всего уроков, мой прогресс, сами уровни.
    expect($count)->toBe(3);
});
