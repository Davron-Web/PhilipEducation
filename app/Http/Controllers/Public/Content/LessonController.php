<?php

namespace App\Http\Controllers\Public\Content;

use App\Http\Controllers\Controller;
use App\Services\LevelProgressService;
use App\Services\XpService;
use App\Models\Content\Lesson;
use App\Models\System\Level;
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
        // Order by the level's CEFR code first (A1, A2, B1, B2, C1, C2 sorts
        // correctly as a plain string) so lessons form one continuous study
        // path, then by position within that level.
        $lessons = Lesson::with('level')
            ->where('is_published', true)
            ->orderBy(Level::select('code')->whereColumn('levels.id', 'lessons.level_id'))
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
    /**
     * Сколько секунд ученик провёл на странице урока.
     *
     * Значение приходит из браузера, поэтому ему нельзя доверять: ограничиваем
     * четырьмя часами, чтобы подделанное поле не испортило статистику.
     */
    private function secondsSpent(Request $request): int
    {
        return max(0, min((int) $request->input('seconds_spent', 0), 4 * 3600));
    }

    public function complete(Request $request, $id, XpService $xp, LevelProgressService $levels): RedirectResponse
    {
        $user = Auth::user();
        if (! $user) {
            return redirect()->route('login')->with('error', 'Please sign in first.');
        }

        $lesson = Lesson::findOrFail($id);

        $progress = UserProgress::where('user_id', $user->id)->where('lesson_id', $lesson->id)->first();

        if ($progress && $progress->is_completed) {
            return redirect()->route('public.lessons.show', $id)->with('info', 'You have already completed this lesson.');
        }

        UserProgress::updateOrCreate(
            ['user_id' => $user->id, 'lesson_id' => $lesson->id],
            [
                'progress_percent' => 100,
                'is_completed' => true,
                'completed_at' => now(),
                // Время на уроке приходит со страницы; без него дашборд
                // показывал бы 0 часов при реальной учёбе.
                'time_spent' => $this->secondsSpent($request),
            ]
        );

        $xp->award($user, 'lesson', $lesson->id);
        $levels->checkAfterLesson($user, $lesson);

        return redirect()->route('public.lessons.show', $id)
            ->with('success', 'Lesson completed! Great work.');
    }
}
