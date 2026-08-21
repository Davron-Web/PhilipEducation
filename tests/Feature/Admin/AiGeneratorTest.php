<?php

use App\Models\Content\Lesson;
use App\Models\System\Level;
use App\Models\User;
use App\Models\User\Role;
use App\Services\GeminiService;

// role_id == 1 is treated as admin app-wide, so always reserve id 1 for the
// admin role first — otherwise a lone "student" role created in an empty
// test database could land on id 1 and be misread as admin.
$adminRole = fn () => Role::firstOrCreate(['name' => 'admin'], ['description' => 'Администратор платформы']);
$studentRole = function () use ($adminRole) {
    $adminRole();

    return Role::firstOrCreate(['name' => 'student'], ['description' => 'Ученик платформы']);
};
$admin = fn () => User::factory()->create(['role_id' => $adminRole()->id]);

function fakeLessonPack(): array
{
    return [
        'lesson' => [
            'title' => 'Present Simple',
            'level' => 'Beginner A1',
            'theory' => 'The present simple is used for habits.',
        ],
        'words' => [
            ['word' => 'go', 'translation' => 'идти'],
            ['word' => 'do', 'translation' => 'делать'],
        ],
        'exercises' => [
            [
                'title' => 'Fill in the blanks',
                'questions' => [
                    ['question' => 'I ___ to school.', 'answer' => 'go'],
                ],
            ],
        ],
        'test' => [
            'title' => 'Тест: Present Simple',
            'questions' => [
                [
                    'question' => 'She ___ to school every day.',
                    'options' => ['go', 'goes', 'going', 'gone'],
                    'correct' => 1,
                ],
            ],
        ],
    ];
}

it('blocks guests and students from the AI generator', function () use ($studentRole) {
    $student = User::factory()->create(['role_id' => $studentRole()->id]);

    $this->post('/admin/ai/generate', ['topic' => 'Present Simple'])->assertRedirect('/login');

    $this->actingAs($student)
        ->post('/admin/ai/generate', ['topic' => 'Present Simple'])
        ->assertForbidden();
});

it('persists a full lesson pack from the AI generator', function () use ($admin) {
    Level::factory()->create(['code' => 'A1']);

    $this->mock(GeminiService::class, function ($mock) {
        $mock->shouldReceive('generateLessonPack')
            ->once()
            ->with('Present Simple')
            ->andReturn(fakeLessonPack());
    });

    $response = $this->actingAs($admin())->post('/admin/ai/generate', [
        'topic' => 'Present Simple',
    ]);

    $response->assertSessionHasNoErrors();
    $response->assertSessionHas('lessonId');

    $lesson = Lesson::with(['words.translations', 'exercises.questions', 'tests.questions.answers'])
        ->findOrFail(session('lessonId'));

    expect($lesson->title)->toBe('Present Simple');
    expect($lesson->is_published)->toBeFalsy();
    expect($lesson->words)->toHaveCount(2);
    expect($lesson->words->first()->translations->first()->translation)->toBe('идти');
    expect($lesson->exercises)->toHaveCount(1);
    expect($lesson->exercises->first()->questions)->toHaveCount(1);
    expect($lesson->tests)->toHaveCount(1);

    $test = $lesson->tests->first();
    expect($test->questions)->toHaveCount(1);
    $question = $test->questions->first();
    expect($question->answers)->toHaveCount(4);
    expect($question->answers->where('is_correct', true)->first()->answer)->toBe('goes');
});

it('shows an error and does not create a lesson when Gemini fails', function () use ($admin) {
    $this->mock(GeminiService::class, function ($mock) {
        $mock->shouldReceive('generateLessonPack')
            ->once()
            ->andThrow(new RuntimeException('Gemini HTTP 500'));
    });

    $response = $this->actingAs($admin())->post('/admin/ai/generate', [
        'topic' => 'Present Simple',
    ]);

    $response->assertSessionHasErrors('topic');
    $this->assertDatabaseCount('lessons', 0);
});
