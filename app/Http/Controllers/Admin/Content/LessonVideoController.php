<?php

namespace App\Http\Controllers\Admin\Content;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Content\StoreLessonVideoRequest;
use App\Http\Requests\Admin\Content\UpdateLessonVideoRequest;
use App\Models\Content\Lesson;
use App\Models\Content\LessonVideo;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class LessonVideoController extends Controller
{
    public function index(): View
    {
        $videos = LessonVideo::with('lesson')
            ->when(request('search'), fn ($q, $s) => $q->where('title', 'like', "%{$s}%"))
            ->when(request('lesson_id'), fn ($q, $id) => $q->where('lesson_id', $id))
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();

        return view('admin.content.lesson-videos.index', [
            'videos' => $videos,
            'lessons' => $this->lessonOptions(),
        ]);
    }

    public function create(): View
    {
        return view('admin.content.lesson-videos.create', [
            'lessons' => $this->lessonOptions(),
            // Позволяет прийти со страницы урока с уже выбранным уроком.
            'selectedLesson' => request('lesson_id'),
        ]);
    }

    public function store(StoreLessonVideoRequest $request): RedirectResponse
    {
        $video = LessonVideo::create($request->validated());

        return redirect()
            ->route('admin.content.lessonvideos.index', ['lesson_id' => $video->lesson_id])
            ->with('success', $video->embedUrl()
                ? 'Видео добавлено'
                : 'Видео добавлено, но ссылку не удалось разобрать — плеер не встроится. Поддерживаются YouTube и Vimeo.');
    }

    public function edit(LessonVideo $lessonvideo): View
    {
        return view('admin.content.lesson-videos.edit', [
            'video' => $lessonvideo,
            'lessons' => $this->lessonOptions(),
        ]);
    }

    public function update(UpdateLessonVideoRequest $request, LessonVideo $lessonvideo): RedirectResponse
    {
        $lessonvideo->update($request->validated());

        return redirect()
            ->route('admin.content.lessonvideos.index', ['lesson_id' => $lessonvideo->lesson_id])
            ->with('success', 'Видео обновлено');
    }

    public function destroy(LessonVideo $lessonvideo): RedirectResponse
    {
        $lesson = $lessonvideo->lesson_id;
        $lessonvideo->delete();

        return redirect()
            ->route('admin.content.lessonvideos.index', ['lesson_id' => $lesson])
            ->with('success', 'Видео удалено');
    }

    /** @return \Illuminate\Support\Collection<int, string> */
    private function lessonOptions()
    {
        return Lesson::orderBy('title')->pluck('title', 'id');
    }
}
