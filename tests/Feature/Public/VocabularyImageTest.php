<?php

use App\Models\Content\Lesson;
use App\Models\User;
use App\Models\Vocabulary\Word;

$user = fn () => User::factory()->create();

it('does not show a word photo on the vocabulary list even when one is set', function () use ($user) {
    $lesson = Lesson::factory()->create();
    Word::factory()->create([
        'lesson_id' => $lesson->id,
        'word' => 'apple',
        'image' => 'assets/images/words/apple.jpg',
    ]);

    $response = $this->actingAs($user())->get('/words');

    $response->assertOk();
    $response->assertDontSee('assets/images/words/apple.jpg', false);
});

it('does not show a word photo on the word detail page', function () use ($user) {
    $lesson = Lesson::factory()->create();
    $word = Word::factory()->create([
        'lesson_id' => $lesson->id,
        'word' => 'banana',
        'image' => 'assets/images/words/banana.jpg',
    ]);

    $response = $this->actingAs($user())->get("/words/{$word->id}");

    $response->assertOk();
    $response->assertDontSee('assets/images/words/banana.jpg', false);
});
