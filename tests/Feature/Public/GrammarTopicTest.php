<?php

use App\Models\Content\GrammarTopic;
use App\Models\Content\Lesson;
use App\Models\Exercise\Exercise;
use App\Models\System\Level;
use App\Models\User;

$user = fn () => User::factory()->create();

it('redirects guests away from grammar topics', function () {
    $this->get('/grammartopics')->assertRedirect('/login');
});

it('lists grammar topics grouped by level progression', function () use ($user) {
    $a1 = Level::factory()->create(['code' => 'A1']);
    $b1 = Level::factory()->create(['code' => 'B1']);

    GrammarTopic::factory()->create(['level_id' => $b1->id, 'title' => 'B1 Topic']);
    GrammarTopic::factory()->create(['level_id' => $a1->id, 'title' => 'A1 Topic']);

    // "/grammartopics" alone is now the category picker; "?category=all"
    // is the flat, level-ordered list of every topic.
    $response = $this->actingAs($user())->get('/grammartopics?category=all');
    $response->assertOk();

    $body = $response->getContent();
    expect(strpos($body, 'A1 Topic'))->toBeLessThan(strpos($body, 'B1 Topic'));
});

it('shows the topic theory and links to its lesson exercises as practice tasks', function () use ($user) {
    $lesson = Lesson::factory()->create();
    $topic = GrammarTopic::factory()->create(['theory' => 'Present Simple is used for habits.']);
    $lesson->update(['grammar_topic_id' => $topic->id]);
    $exercise = Exercise::factory()->create(['lesson_id' => $lesson->id, 'title' => 'Fill in the blanks: Present Simple']);

    $response = $this->actingAs($user())->get("/grammartopics/{$topic->id}");

    $response->assertOk();
    $response->assertSee('Present Simple is used for habits.');
    $response->assertSee('Fill in the blanks: Present Simple');
});

it('hides the practice tasks section when no lesson is linked', function () use ($user) {
    $topic = GrammarTopic::factory()->create();

    $this->actingAs($user())
        ->get("/grammartopics/{$topic->id}")
        ->assertOk()
        ->assertDontSee('Practice tasks');
});
