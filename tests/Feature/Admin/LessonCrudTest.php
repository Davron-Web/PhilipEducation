<?php

use App\Models\Content\Lesson;
use App\Models\System\Level;
use App\Models\User;
use App\Models\User\Role;

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

it('blocks guests and students from the admin lesson list', function () use ($student) {
    $this->get('/admin/content/lessons')->assertRedirect('/login');

    $this->actingAs($student())
        ->get('/admin/content/lessons')
        ->assertForbidden();
});

it('lists lessons grouped by level progression, not interleaved by raw order_number', function () use ($admin) {
    $a1 = Level::factory()->create(['code' => 'A1']);
    $b1 = Level::factory()->create(['code' => 'B1']);

    Lesson::factory()->create(['level_id' => $b1->id, 'order_number' => 1, 'title' => 'B1 Lesson One']);
    Lesson::factory()->create(['level_id' => $a1->id, 'order_number' => 1, 'title' => 'A1 Lesson One']);
    Lesson::factory()->create(['level_id' => $a1->id, 'order_number' => 2, 'title' => 'A1 Lesson Two']);

    $response = $this->actingAs($admin())->get('/admin/content/lessons');
    $response->assertOk();

    $body = $response->getContent();
    $posA1First = strpos($body, 'A1 Lesson One');
    $posA1Second = strpos($body, 'A1 Lesson Two');
    $posB1First = strpos($body, 'B1 Lesson One');

    expect($posA1First)->toBeLessThan($posA1Second);
    expect($posA1Second)->toBeLessThan($posB1First);
});

it('lets an admin list lessons', function () use ($admin) {
    Lesson::factory()->count(3)->create();

    $this->actingAs($admin())
        ->get('/admin/content/lessons')
        ->assertOk()
        ->assertViewIs('admin.content.lessons.index');
});

it('lets an admin create a lesson', function () use ($admin) {
    $level = Level::factory()->create();

    $response = $this->actingAs($admin())->post('/admin/content/lessons', [
        'title' => 'Present Simple',
        'level_id' => $level->id,
        'estimated_minutes' => 20,
        'is_published' => 1,
    ]);

    $response->assertRedirect(route('admin.content.lessons.index'));
    $this->assertDatabaseHas('lessons', [
        'title' => 'Present Simple',
        'level_id' => $level->id,
    ]);
});

it('requires a title to create a lesson', function () use ($admin) {
    $response = $this->actingAs($admin())->post('/admin/content/lessons', [
        'title' => '',
    ]);

    $response->assertSessionHasErrors('title');
    $this->assertDatabaseCount('lessons', 0);
});

it('lets an admin update a lesson', function () use ($admin) {
    $lesson = Lesson::factory()->create(['title' => 'Old title']);

    $response = $this->actingAs($admin())->put("/admin/content/lessons/{$lesson->id}", [
        'title' => 'New title',
        'estimated_minutes' => 15,
    ]);

    $response->assertRedirect(route('admin.content.lessons.index'));
    $this->assertDatabaseHas('lessons', ['id' => $lesson->id, 'title' => 'New title']);
});

it('lets an admin delete a lesson with no user progress', function () use ($admin) {
    $lesson = Lesson::factory()->create();

    $response = $this->actingAs($admin())->delete("/admin/content/lessons/{$lesson->id}");

    $response->assertRedirect(route('admin.content.lessons.index'));
    $this->assertDatabaseMissing('lessons', ['id' => $lesson->id]);
});
