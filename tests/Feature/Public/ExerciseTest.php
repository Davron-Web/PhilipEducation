<?php

use App\Models\Content\Lesson;
use App\Models\Exercise\Exercise;
use App\Models\Exercise\ExerciseQuestion;
use App\Models\User;

$user = fn () => User::factory()->create();

it('redirects guests away from exercises', function () {
    $this->get('/exercises')->assertRedirect('/login');
});

it('lists exercises for an authenticated user', function () use ($user) {
    $lesson = Lesson::factory()->create();
    Exercise::factory()->create(['lesson_id' => $lesson->id, 'title' => 'Fill in the blanks']);

    $this->actingAs($user())
        ->get('/exercises')
        ->assertOk()
        ->assertSee('Fill in the blanks');
});

it('shows an exercise with its questions', function () use ($user) {
    $lesson = Lesson::factory()->create();
    $exercise = Exercise::factory()->create(['lesson_id' => $lesson->id]);
    ExerciseQuestion::factory()->create(['exercise_id' => $exercise->id, 'question' => 'I ___ to school.']);

    $this->actingAs($user())
        ->get("/exercises/{$exercise->id}")
        ->assertOk()
        ->assertSee('I ___ to school.');
});

it('404s for a non-existent exercise', function () use ($user) {
    $this->actingAs($user())
        ->get('/exercises/999999')
        ->assertNotFound();
});
