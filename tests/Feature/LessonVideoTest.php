<?php

use App\Models\Content\Lesson;
use App\Models\Content\LessonVideo;
use App\Models\System\Level;
use App\Models\User;
use App\Models\User\Role;

function lessonWithVideo(array $attributes = []): LessonVideo
{
    $level = Level::factory()->create(['code' => 'A1']);
    $lesson = Lesson::factory()->create(['is_published' => true, 'level_id' => $level->id]);

    return LessonVideo::create(array_merge([
        'lesson_id' => $lesson->id,
        'title' => 'Видеоурок',
        'url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
        'is_published' => true,
    ], $attributes));
}

it('разбирает все формы ссылок YouTube', function (string $url) {
    $video = new LessonVideo(['url' => $url]);

    expect($video->embedUrl())->toBe('https://www.youtube.com/embed/dQw4w9WgXcQ?rel=0');
})->with([
    'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
    'https://youtube.com/watch?list=PL123&v=dQw4w9WgXcQ',
    'https://youtu.be/dQw4w9WgXcQ',
    'https://www.youtube.com/embed/dQw4w9WgXcQ',
    'https://www.youtube.com/shorts/dQw4w9WgXcQ',
]);

it('разбирает ссылку Vimeo', function () {
    $video = new LessonVideo(['url' => 'https://vimeo.com/123456789']);

    expect($video->embedUrl())->toBe('https://player.vimeo.com/video/123456789');
});

it('не встраивает неизвестные ссылки', function (string $url) {
    // Вставлять произвольный адрес в iframe нельзя: страница урока
    // отдала бы часть себя чужому сайту.
    expect((new LessonVideo(['url' => $url]))->embedUrl())->toBeNull();
})->with([
    'https://example.com/video/123',
    'javascript:alert(1)',
    '',
]);

it('показывает видео внизу страницы урока', function () {
    $video = lessonWithVideo(['title' => 'Разбор Present Simple']);
    $user = User::factory()->create(['role_id' => Role::factory()->student()->create()->id]);

    $this->actingAs($user)
        ->get("/public/lessons/{$video->lesson_id}")
        ->assertOk()
        ->assertSee('Видеоуроки')
        ->assertSee('Разбор Present Simple')
        ->assertSee('youtube.com/embed/dQw4w9WgXcQ', escape: false);
});

it('не показывает скрытые видео', function () {
    $video = lessonWithVideo(['title' => 'Черновик', 'is_published' => false]);
    $user = User::factory()->create(['role_id' => Role::factory()->student()->create()->id]);

    $this->actingAs($user)
        ->get("/public/lessons/{$video->lesson_id}")
        ->assertOk()
        ->assertDontSee('Черновик')
        ->assertDontSee('Видеоуроки');
});

it('соблюдает порядок сортировки', function () {
    $first = lessonWithVideo(['title' => 'Второе', 'sort_order' => 5]);
    LessonVideo::create([
        'lesson_id' => $first->lesson_id,
        'title' => 'Первое',
        'url' => 'https://youtu.be/aqz-KE-bpKQ',
        'sort_order' => 1,
        'is_published' => true,
    ]);

    $titles = Lesson::find($first->lesson_id)->publishedVideos->pluck('title')->all();

    expect($titles)->toBe(['Первое', 'Второе']);
});

it('не пускает ученика в управление видео', function () {
    $user = User::factory()->create(['role_id' => Role::factory()->student()->create()->id]);

    $this->actingAs($user)->get('/admin/content/lesson-videos')->assertForbidden();
});

it('позволяет администратору добавить видео', function () {
    $admin = User::factory()->create(['role_id' => Role::factory()->admin()->create()->id]);
    $lesson = Lesson::factory()->create(['is_published' => true]);

    $this->actingAs($admin)
        ->post('/admin/content/lesson-videos', [
            'lesson_id' => $lesson->id,
            'title' => 'Новое видео',
            'url' => 'https://youtu.be/dQw4w9WgXcQ',
            'is_published' => 1,
        ])
        ->assertRedirect();

    expect($lesson->videos()->count())->toBe(1);
});

it('отклоняет ссылку не по http', function () {
    $admin = User::factory()->create(['role_id' => Role::factory()->admin()->create()->id]);
    $lesson = Lesson::factory()->create();

    // Иначе в src плеера можно положить javascript: и выполнить чужой код.
    $this->actingAs($admin)
        ->post('/admin/content/lesson-videos', [
            'lesson_id' => $lesson->id,
            'title' => 'Плохое',
            'url' => 'javascript:alert(1)',
        ])
        ->assertSessionHasErrors('url');

    expect(LessonVideo::count())->toBe(0);
});

it('удаляет видео вместе с уроком', function () {
    $video = lessonWithVideo();

    Lesson::find($video->lesson_id)->delete();

    expect(LessonVideo::find($video->id))->toBeNull();
});
