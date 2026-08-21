<?php

namespace App\Http\Controllers\Admin\Content;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Content\StoreLessonContentRequest;
use App\Http\Requests\Admin\Content\UpdateLessonContentRequest;
use App\Models\Content\Lesson;
use App\Models\Content\LessonContent;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class LessonContentController extends Controller
{
    public function index(): View
    {
        $contents = LessonContent::with('lesson')
            ->when(request('lesson_id'), function ($query, $lessonId) {
                $query->where('lesson_id', $lessonId);
            })
            ->when(request('type'), function ($query, $type) {
                $query->where('type', $type);
            })
            ->orderBy('order_number')
            ->paginate(request('per_page', 20))
            ->withQueryString();

        $lessons = Lesson::orderBy('order_number')->pluck('title', 'id');

        return view('admin.content.lessoncontents.index', compact('contents', 'lessons'));
    }

    public function create(): View
    {
        $lessons = Lesson::orderBy('order_number')->get();

        return view('admin.content.lessoncontents.create', compact('lessons'));
    }

    public function store(StoreLessonContentRequest $request): RedirectResponse
    {
        $data = $request->validated();

        if (empty($data['order_number'])) {
            $data['order_number'] = LessonContent::where('lesson_id', $data['lesson_id'])->max('order_number') + 1;
        }

        $content = LessonContent::create($data);

        return redirect()
            ->route('admin.content.lessons.edit', $content->lesson_id)
            ->with('success', 'Контент урока создан');
    }

    public function show(LessonContent $lessoncontent): View
    {
        return view('admin.content.lessoncontents.show', [
            'lessonContent' => $lessoncontent->load('lesson'),
        ]);
    }

    public function edit(LessonContent $lessoncontent): View
    {
        $lessons = Lesson::orderBy('order_number')->get();

        return view('admin.content.lessoncontents.edit', ['lessonContent' => $lessoncontent, 'lessons' => $lessons]);
    }

    public function update(UpdateLessonContentRequest $request, LessonContent $lessoncontent): RedirectResponse
    {
        $lessoncontent->update($request->validated());

        return redirect()
            ->route('admin.content.lessons.edit', $lessoncontent->lesson_id)
            ->with('success', 'Контент урока обновлён');
    }

    public function destroy(LessonContent $lessoncontent): RedirectResponse
    {
        $lessonId = $lessoncontent->lesson_id;
        $lessoncontent->delete();

        return redirect()
            ->route('admin.content.lessons.edit', $lessonId)
            ->with('success', 'Контент урока удалён');
    }
}
