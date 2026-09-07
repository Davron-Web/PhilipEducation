<?php

use App\Models\Test\Test;
use App\Models\Test\TestAnswer;
use App\Models\Test\TestQuestion;
use App\Models\User;
use App\Models\User\Role;
use App\Services\TestGradingService;

function explainedTest(): Test
{
    $test = Test::factory()->create(['passing_score' => 50]);

    $question = TestQuestion::factory()->singleChoice()->create([
        'test_id' => $test->id,
        'question' => 'She ___ to school every day.',
        'topic' => 'present-simple',
        'explanation' => 'В Present Simple к глаголу для he/she/it добавляется -s.',
    ]);

    TestAnswer::create(['question_id' => $question->id, 'answer' => 'goes', 'is_correct' => true, 'sort_order' => 0]);
    TestAnswer::create(['question_id' => $question->id, 'answer' => 'go', 'is_correct' => false, 'sort_order' => 1]);

    return $test->fresh();
}

function explainStudent(): User
{
    return User::factory()->create([
        'role_id' => Role::factory()->student()->create()->id,
        'is_active' => true,
    ]);
}

it('показывает объяснение после неверного ответа', function () {
    $test = explainedTest();
    $user = explainStudent();
    $question = $test->questions->first();
    $wrong = $question->answers->where('is_correct', false)->first();

    $attempt = app(TestGradingService::class)->grade($user, $test, [$question->id => (string) $wrong->id]);

    $this->actingAs($user)
        ->get("/tests/attempts/{$attempt->id}")
        ->assertOk()
        ->assertSee('добавляется -s', escape: false);
});

it('не показывает объяснение при верном ответе', function () {
    $test = explainedTest();
    $user = explainStudent();
    $question = $test->questions->first();
    $right = $question->answers->where('is_correct', true)->first();

    $attempt = app(TestGradingService::class)->grade($user, $test, [$question->id => (string) $right->id]);

    // После верного ответа объяснение только удлиняет разбор.
    $this->actingAs($user)
        ->get("/tests/attempts/{$attempt->id}")
        ->assertOk()
        ->assertDontSee('добавляется -s', escape: false);
});

it('не отдаёт объяснение в API до ответа', function () {
    $test = explainedTest();
    $user = explainStudent();

    // Иначе клиент получил бы разбор вместе с самим вопросом.
    $body = $this->actingAs($user, 'sanctum')
        ->getJson("/api/v1/tests/{$test->id}")
        ->assertOk()
        ->json();

    expect(json_encode($body, JSON_UNESCAPED_UNICODE))->not->toContain('добавляется -s');
});

it('обходится без объяснения, когда его не задали', function () {
    $test = Test::factory()->create(['passing_score' => 50]);
    $question = TestQuestion::factory()->singleChoice()->create(['test_id' => $test->id, 'explanation' => null]);
    TestAnswer::create(['question_id' => $question->id, 'answer' => 'a', 'is_correct' => true, 'sort_order' => 0]);
    TestAnswer::create(['question_id' => $question->id, 'answer' => 'b', 'is_correct' => false, 'sort_order' => 1]);

    $user = explainStudent();
    $wrong = $question->answers->where('is_correct', false)->first();
    $attempt = app(TestGradingService::class)->grade($user, $test->fresh(), [$question->id => (string) $wrong->id]);

    $this->actingAs($user)->get("/tests/attempts/{$attempt->id}")->assertOk();
});
