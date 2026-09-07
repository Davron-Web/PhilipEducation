<?php

use App\Models\Test\Test;
use App\Models\Test\TestAnswer;
use App\Models\Test\TestQuestion;
use App\Models\User;
use App\Models\User\Role;
use App\Services\TestGradingService;
use App\Services\TopicStatsService;

function statsStudent(): User
{
    return User::factory()->create([
        'role_id' => Role::factory()->student()->create()->id,
        'is_active' => true,
    ]);
}

/** Тест из $count вопросов одной темы; отвечаем верно на первые $correct. */
function answerTopic(User $user, string $topic, int $count, int $correct): void
{
    $test = Test::factory()->create(['passing_score' => 50]);
    $answers = [];

    for ($i = 0; $i < $count; $i++) {
        $question = TestQuestion::factory()->singleChoice()->create([
            'test_id' => $test->id,
            'topic' => $topic,
        ]);

        $right = TestAnswer::create(['question_id' => $question->id, 'answer' => 'right', 'is_correct' => true, 'sort_order' => 0]);
        $wrong = TestAnswer::create(['question_id' => $question->id, 'answer' => 'wrong', 'is_correct' => false, 'sort_order' => 1]);

        $answers[$question->id] = (string) ($i < $correct ? $right->id : $wrong->id);
    }

    app(TestGradingService::class)->grade($user, $test->fresh(), $answers);
}

it('считает долю верных ответов по теме', function () {
    $user = statsStudent();
    answerTopic($user, 'articles', 4, 3);

    $row = app(TopicStatsService::class)->forUser($user)->firstWhere('topic', 'articles');

    expect($row['total'])->toBe(4)
        ->and($row['correct'])->toBe(3)
        ->and($row['wrong'])->toBe(1)
        ->and($row['accuracy'])->toBe(0.75);
});

it('помечает тему слабой ниже порога', function () {
    $user = statsStudent();
    answerTopic($user, 'passive-voice', 5, 1);

    $row = app(TopicStatsService::class)->forUser($user)->firstWhere('topic', 'passive-voice');

    expect($row['is_weak'])->toBeTrue();
});

it('не считает тему слабой по паре ответов', function () {
    $user = statsStudent();
    answerTopic($user, 'modals', 2, 0);

    // Две ошибки подряд — ещё не вывод о теме.
    $row = app(TopicStatsService::class)->forUser($user)->firstWhere('topic', 'modals');

    expect($row['total'])->toBe(2)
        ->and($row['is_weak'])->toBeFalse();
});

it('показывает слабые темы первыми', function () {
    $user = statsStudent();
    answerTopic($user, 'articles', 4, 4);
    answerTopic($user, 'passive-voice', 4, 1);

    $topics = app(TopicStatsService::class)->forUser($user)->pluck('topic')->all();

    expect($topics[0])->toBe('passive-voice');
});

it('не смешивает статистику разных учеников', function () {
    $mine = statsStudent();
    $other = statsStudent();

    answerTopic($mine, 'articles', 3, 3);
    answerTopic($other, 'modals', 3, 0);

    $topics = app(TopicStatsService::class)->forUser($mine)->pluck('topic')->all();

    expect($topics)->toBe(['articles']);
});

it('пропускает вопросы без темы', function () {
    $user = statsStudent();
    answerTopic($user, 'articles', 3, 2);

    $test = Test::factory()->create(['passing_score' => 50]);
    $question = TestQuestion::factory()->singleChoice()->create(['test_id' => $test->id, 'topic' => null]);
    TestAnswer::create(['question_id' => $question->id, 'answer' => 'a', 'is_correct' => true, 'sort_order' => 0]);
    app(TestGradingService::class)->grade($user, $test->fresh(), [$question->id => 'нет']);

    expect(app(TopicStatsService::class)->forUser($user)->count())->toBe(1);
});

it('отдаёт только слабые темы отдельным списком', function () {
    $user = statsStudent();
    answerTopic($user, 'articles', 4, 4);
    answerTopic($user, 'passive-voice', 4, 1);
    answerTopic($user, 'modals', 4, 0);

    $weak = app(TopicStatsService::class)->weakTopics($user)->pluck('topic')->all();

    expect($weak)->toBe(['modals', 'passive-voice']);
});

it('показывает статистику тем в профиле', function () {
    $user = statsStudent();
    answerTopic($user, 'present-simple', 4, 1);

    $this->actingAs($user)
        ->get('/profiles')
        ->assertOk()
        ->assertSee('Present Simple')
        ->assertSee('слабая');
});
