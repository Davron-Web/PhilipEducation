<?php

use App\Models\Content\Lesson;
use App\Models\User;
use App\Models\Vocabulary\Word;

$user = fn () => User::factory()->create();

it('shows a word photo on the vocabulary list when one is set', function () use ($user) {
    $lesson = Lesson::factory()->create();
    Word::factory()->create([
        'lesson_id' => $lesson->id,
        'word' => 'apple',
        'image' => 'assets/images/words/apple.jpg',
    ]);

    $response = $this->actingAs($user())->get('/words');

    $response->assertOk();
    $response->assertSee('assets/images/words/apple.jpg', false);
});

it('does not render a broken image tag when a word has no photo', function () use ($user) {
    $lesson = Lesson::factory()->create();
    Word::factory()->create(['lesson_id' => $lesson->id, 'word' => 'nophoto', 'image' => null]);

    $response = $this->actingAs($user())->get('/words');

    $response->assertOk();
    $response->assertDontSee('<img src="http://localhost/" alt="nophoto"', false);
});

it('shows the word photo on the word detail page', function () use ($user) {
    $lesson = Lesson::factory()->create();
    $word = Word::factory()->create([
        'lesson_id' => $lesson->id,
        'word' => 'banana',
        'image' => 'assets/images/words/banana.jpg',
    ]);

    $response = $this->actingAs($user())->get("/words/{$word->id}");

    $response->assertOk();
    $response->assertSee('assets/images/words/banana.jpg', false);
});
