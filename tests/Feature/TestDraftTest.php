<?php

use App\Models\Test\Test;
use App\Models\Test\TestAnswer;
use App\Models\Test\TestDraft;
use App\Models\Test\TestQuestion;
use App\Models\User;
use App\Models\User\Role;

function draftStudent(): User
{
    return User::factory()->create([
        'role_id' => Role::factory()->student()->create()->id,
        'is_active' => true,
    ]);
}

function draftTest(): Test
{
    $test = Test::factory()->create(['is_published' => true, 'passing_score' => 50]);

    foreach (range(1, 3) as $i) {
        $question = TestQuestion::factory()->singleChoice()->create([
            'test_id' => $test->id,
            'question' => "Вопрос {$i}",
        ]);
        TestAnswer::create(['question_id' => $question->id, 'answer' => 'right', 'is_correct' => true, 'sort_order' => 0]);
        TestAnswer::create(['question_id' => $question->id, 'answer' => 'wrong', 'is_correct' => false, 'sort_order' => 1]);
    }

    return $test->fresh();
}

it('сохраняет незавершённый тест', function () {
    $user = draftStudent();
    $test = draftTest();
    $question = $test->questions->first();
    $answer = $question->answers->first();

    $this->actingAs($user)
        ->post("/tests/{$test->id}/draft", [
            'answers' => [$question->id => (string) $answer->id],
            'seconds_spent' => 42,
        ])
        ->assertNoContent();

    $draft = TestDraft::where('user_id', $user->id)->where('test_id', $test->id)->first();

    expect($draft)->not->toBeNull()
        ->and($draft->seconds_spent)->toBe(42)
        ->and($draft->answeredCount())->toBe(1);
});

it('держит один черновик на тест, а не копит их', function () {
    $user = draftStudent();
    $test = draftTest();
    $question = $test->questions->first();

    foreach ([1, 2] as $seconds) {
        $this->actingAs($user)->post("/tests/{$test->id}/draft", [
            'answers' => [$question->id => (string) $question->answers->first()->id],
            'seconds_spent' => $seconds * 10,
        ]);
    }

    expect(TestDraft::where('user_id', $user->id)->count())->toBe(1)
        ->and(TestDraft::first()->seconds_spent)->toBe(20);
});

it('восстанавливает отмеченные ответы при возврате', function () {
    $user = draftStudent();
    $test = draftTest();
    $question = $test->questions->first();
    $answer = $question->answers->first();

    TestDraft::create([
        'user_id' => $user->id,
        'test_id' => $test->id,
        'answers' => [$question->id => (string) $answer->id],
        'seconds_spent' => 30,
    ]);

    $this->actingAs($user)
        ->get("/tests/{$test->id}")
        ->assertOk()
        ->assertSee('Ответы восстановлены')
        ->assertSee('value="'.$answer->id.'"', escape: false)
        ->assertSee('checked', escape: false);
});

it('не показывает черновик другого ученика', function () {
    $mine = draftStudent();
    $other = draftStudent();
    $test = draftTest();

    TestDraft::create([
        'user_id' => $other->id,
        'test_id' => $test->id,
        'answers' => [$test->questions->first()->id => '1'],
    ]);

    $this->actingAs($mine)
        ->get("/tests/{$test->id}")
        ->assertOk()
        ->assertDontSee('Ответы восстановлены');
});

it('удаляет черновик после отправки теста', function () {
    $user = draftStudent();
    $test = draftTest();
    $question = $test->questions->first();

    TestDraft::create([
        'user_id' => $user->id,
        'test_id' => $test->id,
        'answers' => [$question->id => (string) $question->answers->first()->id],
    ]);

    $this->actingAs($user)->post("/tests/{$test->id}/submit", [
        'answers' => [$question->id => (string) $question->answers->first()->id],
    ])->assertRedirect();

    // Тест сдан — черновик больше не результат и не должен всплывать.
    expect(TestDraft::where('user_id', $user->id)->count())->toBe(0);
});

it('позволяет начать заново', function () {
    $user = draftStudent();
    $test = draftTest();

    TestDraft::create([
        'user_id' => $user->id,
        'test_id' => $test->id,
        'answers' => [$test->questions->first()->id => '1'],
    ]);

    $this->actingAs($user)
        ->delete("/tests/{$test->id}/draft")
        ->assertRedirect(route('tests.show', $test->id));

    expect(TestDraft::count())->toBe(0);
});

it('не создаёт попытку из черновика', function () {
    $user = draftStudent();
    $test = draftTest();

    $this->actingAs($user)->post("/tests/{$test->id}/draft", [
        'answers' => [$test->questions->first()->id => '1'],
    ]);

    // Черновик — не результат: он не должен попадать в статистику
    // сданных тестов и в историю попыток.
    expect($user->results()->count())->toBe(0)
        ->and(App\Models\Test\TestAttempt::count())->toBe(0);
});

it('не пускает гостя к автосохранению', function () {
    $test = draftTest();

    $this->post("/tests/{$test->id}/draft", ['answers' => []])->assertRedirect('/login');
});
