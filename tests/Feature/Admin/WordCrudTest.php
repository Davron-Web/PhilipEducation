<?php

use App\Models\Content\Lesson;
use App\Models\User;
use App\Models\User\Role;
use App\Models\Vocabulary\Word;

// role_id == 1 is treated as admin app-wide, so always reserve id 1 for the
// admin role first — otherwise a lone "student" role created in an empty
// test database could land on id 1 and be misread as admin.
$adminRole = fn () => Role::firstOrCreate(['name' => 'admin'], ['description' => 'Администратор платформы']);
$studentRole = function () use ($adminRole) {
    $adminRole();

    return Role::firstOrCreate(['name' => 'student'], ['description' => 'Ученик платформы']);
};
$admin = fn () => User::factory()->create(['role_id' => $adminRole()->id]);
$student = fn () => User::factory()->create(['role_id' => $studentRole()->id]);

it('blocks guests and students from the admin word list', function () use ($student) {
    $this->get('/admin/vocabulary/words')->assertRedirect('/login');

    $this->actingAs($student())
        ->get('/admin/vocabulary/words')
        ->assertForbidden();
});

it('lets an admin list words', function () use ($admin) {
    $lesson = Lesson::factory()->create();
    Word::factory()->create(['lesson_id' => $lesson->id, 'word' => 'apple']);

    $this->actingAs($admin())
        ->get('/admin/vocabulary/words')
        ->assertOk()
        ->assertSee('apple');
});

it('lets an admin create a word', function () use ($admin) {
    $lesson = Lesson::factory()->create();

    $response = $this->actingAs($admin())->post('/admin/vocabulary/words', [
        'lesson_id' => $lesson->id,
        'word' => 'banana',
        'difficulty' => 2,
    ]);

    $response->assertRedirect(route('admin.vocabulary.words.index'));
    $this->assertDatabaseHas('words', ['word' => 'banana', 'lesson_id' => $lesson->id]);
});

it('requires a lesson and a word to create a word', function () use ($admin) {
    $response = $this->actingAs($admin())->post('/admin/vocabulary/words', []);

    $response->assertSessionHasErrors(['lesson_id', 'word', 'difficulty']);
    $this->assertDatabaseCount('words', 0);
});

it('lets an admin update a word', function () use ($admin) {
    $lesson = Lesson::factory()->create();
    $word = Word::factory()->create(['lesson_id' => $lesson->id, 'word' => 'old']);

    $response = $this->actingAs($admin())->put("/admin/vocabulary/words/{$word->id}", [
        'lesson_id' => $lesson->id,
        'word' => 'new',
        'difficulty' => 3,
    ]);

    $response->assertRedirect(route('admin.vocabulary.words.index'));
    $this->assertDatabaseHas('words', ['id' => $word->id, 'word' => 'new']);
});

it('lets an admin delete a word with no user progress', function () use ($admin) {
    $lesson = Lesson::factory()->create();
    $word = Word::factory()->create(['lesson_id' => $lesson->id]);

    $response = $this->actingAs($admin())->delete("/admin/vocabulary/words/{$word->id}");

    $response->assertRedirect(route('admin.vocabulary.words.index'));
    $this->assertDatabaseMissing('words', ['id' => $word->id]);
});
