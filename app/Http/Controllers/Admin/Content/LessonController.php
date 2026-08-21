<?php

namespace App\Http\Controllers\Admin\Content;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Content\StoreLessonRequest;
use App\Http\Requests\Admin\Content\UpdateLessonRequest;
use App\Models\Content\Lesson;
use App\Models\System\Level;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class LessonController extends Controller
{
    public function index(): View
    {
        $lessons = Lesson::with(['level'])
            ->when(request('level_id'), function ($query, $levelId) {
                $query->where('level_id', $levelId);
            })
            ->when(request('search'), function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            })
            ->when(request('is_published') !== null, function ($query) {
                $query->where('is_published', request('is_published'));
            })
            ->orderBy('order_number')
            ->paginate(request('per_page', 20))
            ->withQueryString();

        $levels = Level::orderBy('name')->get();

        return view('admin.content.lessons.index', compact('lessons', 'levels'));
    }

    public function create(): View
    {
        $levels = Level::orderBy('name')->get();

        return view('admin.content.lessons.create', compact('levels'));
    }

    public function store(StoreLessonRequest $request): RedirectResponse
    {
        try {
            $lesson = DB::transaction(function () use ($request) {
                $data = $request->validated();

                if (!isset($data['order_number'])) {
                    $data['order_number'] = Lesson::where('level_id', $data['level_id'])
                            ->max('order_number') + 1;
                }

                return Lesson::create($data);
            });

            return redirect()
                ->route('admin.content.lessons.index')
                ->with('success', 'Lesson created successfully');

        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', $e->getMessage());
        }
    }

    public function show(Lesson $lesson): View
    {
        return view('admin.content.lessons.show', [
            'lesson' => $lesson->load(['level', 'contents', 'words.translations', 'exercises', 'tests'])
        ]);
    }

    public function edit(Lesson $lesson): View
    {
        $levels = Level::orderBy('name')->get();
        $lesson->load('contents');

        return view('admin.content.lessons.edit', compact('lesson', 'levels'));
    }

    public function update(UpdateLessonRequest $request, Lesson $lesson): RedirectResponse
    {
        try {
            $lesson->update($request->validated());

            return redirect()
                ->route('admin.content.lessons.index')
                ->with('success', 'Lesson updated successfully');

        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', $e->getMessage());
        }
    }

    public function destroy(Lesson $lesson): RedirectResponse
    {
        if ($lesson->userProgress()->exists()) {
            return redirect()
                ->route('admin.content.lessons.index')
                ->with('error', 'Cannot delete: users have progress on this lesson');
        }

        try {
            DB::transaction(function () use ($lesson) {
                $lesson->contents()->delete();
                $lesson->comments()->delete();
                $lesson->words()->update(['lesson_id' => null]);
                $lesson->exercises()->each(function ($exercise) {
                    $exercise->userAnswers()->delete();
                    $exercise->questions()->delete();
                    $exercise->delete();
                });
                $lesson->tests()->each(function ($test) {
                    $test->attempts()->delete();
                    $test->results()->delete();
                    $test->questions()->each(function ($question) {
                        $question->userAnswers()->delete();
                        $question->answers()->delete();
                        $question->delete();
                    });
                    $test->delete();
                });
                $lesson->delete();
            });

            return redirect()
                ->route('admin.content.lessons.index')
                ->with('success', 'Lesson deleted successfully');

        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', $e->getMessage());
        }
    }
}
