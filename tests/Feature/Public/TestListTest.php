<?php

use App\Models\Content\Lesson;
use App\Models\System\Level;
use App\Models\Test\Test;
use App\Models\Test\TestAnswer;
use App\Models\Test\TestQuestion;
use App\Models\User;

$user = fn () => User::factory()->create();

it('redirects guests away from the tests list', function () {
    $this->get('/tests')->assertRedirect('/login');
});

it('lists only published tests, grouped by level', function () use ($user) {
    $level = Level::factory()->create(['code' => 'A1']);
    $lesson = Lesson::factory()->create(['level_id' => $level->id]);

    Test::factory()->create(['lesson_id' => $lesson->id, 'title' => 'Published Test', 'is_published' => true]);
    Test::factory()->create(['lesson_id' => $lesson->id, 'title' => 'Draft Test', 'is_published' => false]);

    $response = $this->actingAs($user())->get('/tests');

    $response->assertOk();
    $response->assertSee('Published Test');
    $response->assertDontSee('Draft Test');
});

it('shows a test with its questions and answers', function () use ($user) {
    $lesson = Lesson::factory()->create();
    $test = Test::factory()->create(['lesson_id' => $lesson->id, 'is_published' => true]);
    // Тип пришпилен: у вопросов типа text в test_answers лежит верный
    // ответ, и шаблон намеренно его не показывает.
    $question = TestQuestion::factory()->singleChoice()->create(['test_id' => $test->id, 'question' => 'What is the past tense of "go"?']);
    TestAnswer::factory()->create(['question_id' => $question->id, 'answer' => 'went', 'is_correct' => true]);
    TestAnswer::factory()->create(['question_id' => $question->id, 'answer' => 'goed', 'is_correct' => false]);

    $this->actingAs($user())
        ->get("/tests/{$test->id}")
        ->assertOk()
        ->assertSee('What is the past tense of "go"?')
        ->assertSee('went');
});

it('404s for a non-existent test', function () use ($user) {
    $this->actingAs($user())
        ->get('/tests/999999')
        ->assertNotFound();
});
