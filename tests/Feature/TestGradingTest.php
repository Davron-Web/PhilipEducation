<?php

use App\Models\Content\Lesson;
use App\Models\Test\Test;
use App\Models\Test\TestAnswer;
use App\Models\Test\TestQuestion;
use App\Models\User;
use App\Services\TestGradingService;

/**
 * Собирает тест с одним вопросом-выбором и одним открытым вопросом.
 */
function makeTest(int $passingScore = 60): Test
{
    $lesson = Lesson::factory()->create();

    $test = Test::create([
        'lesson_id' => $lesson->id,
        'title' => 'Проверочный тест',
        'passing_score' => $passingScore,
        'is_published' => true,
    ]);

    $single = TestQuestion::create([
        'test_id' => $test->id,
        'question' => 'Выберите верное',
        'type' => 'single_choice',
        'points' => 1,
    ]);
    TestAnswer::create(['question_id' => $single->id, 'answer' => 'Верно', 'is_correct' => true]);
    TestAnswer::create(['question_id' => $single->id, 'answer' => 'Неверно', 'is_correct' => false]);

    $text = TestQuestion::create([
        'test_id' => $test->id,
        'question' => 'Напишите слово',
        'type' => 'text',
        'points' => 1,
    ]);
    TestAnswer::create(['question_id' => $text->id, 'answer' => 'book', 'is_correct' => true]);

    return $test->load('questions.answers');
}

it('сохраняет попытку и все ответы', function () {
    $user = User::factory()->create();
    $test = makeTest();
    $correctId = $test->questions->first()->answers->where('is_correct', true)->first()->id;

    $attempt = app(TestGradingService::class)->grade($user, $test, [
        $test->questions[0]->id => $correctId,
        $test->questions[1]->id => 'book',
    ]);

    expect($attempt->score)->toBe(100)
        ->and($attempt->passed)->toBeTrue()
        ->and($attempt->answers)->toHaveCount(2);
});

it('считает балл в процентах и проваливает тест ниже проходного', function () {
    $user = User::factory()->create();
    $test = makeTest(passingScore: 60);
    $wrongId = $test->questions->first()->answers->where('is_correct', false)->first()->id;

    // Один из двух вопросов — 50%, при проходном 60% это провал.
    $attempt = app(TestGradingService::class)->grade($user, $test, [
        $test->questions[0]->id => $wrongId,
        $test->questions[1]->id => 'book',
    ]);

    expect($attempt->score)->toBe(50)
        ->and($attempt->passed)->toBeFalse();
});

it('не засчитывает открытый ответ с другим словом, но прощает регистр и пробелы', function () {
    $user = User::factory()->create();
    $test = makeTest();
    $textQuestion = $test->questions[1];

    $wrong = app(TestGradingService::class)->grade($user, $test, [$textQuestion->id => 'pen']);
    $sloppy = app(TestGradingService::class)->grade($user, $test, [$textQuestion->id => '  BOOK ']);

    expect($wrong->answers->where('question_id', $textQuestion->id)->first()->is_correct)->toBeFalse()
        ->and($sloppy->answers->where('question_id', $textQuestion->id)->first()->is_correct)->toBeTrue();
});

it('в multiple_choice засчитывает только полный набор верных вариантов', function () {
    $user = User::factory()->create();
    $lesson = Lesson::factory()->create();
    $test = Test::create(['lesson_id' => $lesson->id, 'title' => 'Мульти', 'passing_score' => 50, 'is_published' => true]);

    $q = TestQuestion::create(['test_id' => $test->id, 'question' => 'Выберите все', 'type' => 'multiple_choice', 'points' => 1]);
    $a1 = TestAnswer::create(['question_id' => $q->id, 'answer' => 'A', 'is_correct' => true]);
    $a2 = TestAnswer::create(['question_id' => $q->id, 'answer' => 'B', 'is_correct' => true]);
    $a3 = TestAnswer::create(['question_id' => $q->id, 'answer' => 'C', 'is_correct' => false]);

    $test->load('questions.answers');
    $service = app(TestGradingService::class);

    $partial = $service->grade($user, $test, [$q->id => [$a1->id]]);
    $everything = $service->grade($user, $test, [$q->id => [$a1->id, $a2->id, $a3->id]]);
    $exact = $service->grade($user, $test, [$q->id => [$a1->id, $a2->id]]);

    expect($partial->passed)->toBeFalse()
        ->and($everything->passed)->toBeFalse()   // «отметить всё» не должно проходить
        ->and($exact->passed)->toBeTrue();
});

it('записывает сводку в user_results, которую читает кабинет', function () {
    $user = User::factory()->create();
    $test = makeTest();
    $correctId = $test->questions->first()->answers->where('is_correct', true)->first()->id;

    app(TestGradingService::class)->grade($user, $test, [
        $test->questions[0]->id => $correctId,
        $test->questions[1]->id => 'book',
    ]);

    expect($user->results()->where('passed', true)->count())->toBe(1);
});

it('не показывает чужую попытку', function () {
    $owner = User::factory()->create();
    $stranger = User::factory()->create();
    $test = makeTest();

    $attempt = app(TestGradingService::class)->grade($owner, $test, []);

    $this->actingAs($stranger)
        ->get(route('tests.result', $attempt->id))
        ->assertForbidden();
});

it('проводит тест через форму и ведёт на результат', function () {
    $user = User::factory()->create();
    $test = makeTest();
    $correctId = $test->questions->first()->answers->where('is_correct', true)->first()->id;

    $response = $this->actingAs($user)->post(route('tests.submit', $test->id), [
        'answers' => [
            $test->questions[0]->id => $correctId,
            $test->questions[1]->id => 'book',
        ],
    ]);

    $attempt = $user->fresh()->testAttempts()->latest('id')->first();

    $response->assertRedirect(route('tests.result', $attempt->id));
    expect($attempt->score)->toBe(100);
});
