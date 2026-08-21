<?php

namespace App\Http\Controllers\Public\Content;

use App\Http\Controllers\Controller;
use App\Models\Content\Lesson;
use App\Models\User\UserProgress;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class LessonController extends Controller
{
    /**
     * Display a listing of lessons.
     */
    public function index(): View
    {
        $lessons = Lesson::with('level')
            ->where('is_published', true)
            ->orderBy('order_number')
            ->get();

        $progressByLesson = Auth::check()
            ? Auth::user()->progress()->pluck('progress_percent', 'lesson_id')
            : collect();

        return view('public.lessons.index', compact('lessons', 'progressByLesson'));
    }

    /**
     * Display the specified lesson.
     */
    public function show($id): View
    {
        $lesson = Lesson::with(['level', 'contents' => function ($query) {
            $query->orderBy('order_number');
        }, 'words.translations', 'tests'])->findOrFail($id);

        $progress = Auth::check()
            ? Auth::user()->progress()->where('lesson_id', $lesson->id)->first()
            : null;

        return view('public.lessons.show', compact('lesson', 'progress'));
    }

    /**
     * Mark a lesson as completed for the current user.
     */
    public function complete(Request $request, $id): RedirectResponse
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login')->with('error', 'Please sign in first.');
        }

        $lesson = Lesson::findOrFail($id);

        $progress = UserProgress::where('user_id', $user->id)->where('lesson_id', $lesson->id)->first();

        if ($progress && $progress->is_completed) {
            return redirect()->route('public.lessons.show', $id)->with('info', 'You have already completed this lesson.');
        }

        UserProgress::updateOrCreate(
            ['user_id' => $user->id, 'lesson_id' => $lesson->id],
            ['progress_percent' => 100, 'is_completed' => true, 'completed_at' => now()]
        );

        return redirect()->route('public.lessons.show', $id)
            ->with('success', 'Lesson completed! Great work.');
    }
}
