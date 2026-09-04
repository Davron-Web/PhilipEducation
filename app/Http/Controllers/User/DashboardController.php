<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Content\Lesson;
use App\Models\Exercise\Exercise;
use App\Models\Gamification\Achievement;
use App\Models\Gamification\Title;
use App\Models\System\Level;
use App\Models\User;
use App\Services\SpacedRepetitionService;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Display the user dashboard with progress stats.
     */
    public function index(SpacedRepetitionService $srs)
    {
        $user = Auth::user();
        $wordsDue = $srs->dueCount($user);

        $lessonsCompleted = $user->progress()->where('is_completed', true)->count();
        $wordsLearned = $user->words()->wherePivot('learned', true)->count();
        $testsPassed = $user->results()->where('passed', true)->count();
        $achievementsCount = $user->achievements()->count();

        $totalLessons = Lesson::where('is_published', true)->count();

        $stats = [
            'lessons_completed' => $lessonsCompleted,
            'lessons_progress' => $totalLessons ? round(min(100, ($lessonsCompleted / $totalLessons) * 100)) : 0,
            'words_learned' => $wordsLearned,
            'words_progress' => round(min(100, ($wordsLearned / 500) * 100)),
            'tests_passed' => $testsPassed,
            'tests_progress' => round(min(100, ($testsPassed / 20) * 100)),
            'achievements_count' => $achievementsCount,
            'achievements_total' => Achievement::count(),
            'hours_studied' => round($user->progress()->sum('time_spent') / 3600, 1),
            'streak' => $this->currentStreak($user),
            'xp' => (int) $user->points,
        ];

        $title = $user->currentTitle();

        // Сколько осталось до следующего звания — прогресс виднее, чем голое
        // число опыта.
        $nextTitle = Title::where('is_active', true)
            ->where('min_xp', '>', $user->points)
            ->orderBy('min_xp')
            ->first();

        $recentXp = $user->xpEvents()->latest()->take(5)->get();

        $nextLesson = Lesson::where('is_published', true)
            ->whereDoesntHave('userProgress', function ($query) use ($user) {
                $query->where('user_id', $user->id)->where('is_completed', true);
            })
            ->orderBy(Level::select('code')->whereColumn('levels.id', 'lessons.level_id'))
            ->orderBy('order_number')
            ->first();

        $dailyExercise = Exercise::whereDoesntHave('userAnswers', function ($query) use ($user) {
                $query->where('user_id', $user->id)->whereDate('created_at', today());
            })
            ->orderBy('id')
            ->first();

        $streakDays = $this->lastSevenDays($user);

        return view('user.dashboard', compact('stats', 'nextLesson', 'dailyExercise', 'streakDays', 'wordsDue', 'title', 'nextTitle', 'recentXp'));
    }

    /**
     * Число дней подряд (включая сегодня, либо вчера — если сегодня ученик
     * ещё не успел позаниматься), в которые был завершён хотя бы один урок.
     */
    private function currentStreak(User $user): int
    {
        $completedDates = $user->progress()
            ->whereNotNull('completed_at')
            ->pluck('completed_at')
            ->map(fn ($date) => $date->toDateString())
            ->unique();

        if ($completedDates->isEmpty()) {
            return 0;
        }

        $cursor = now()->toDateString();
        if (! $completedDates->contains($cursor)) {
            $cursor = now()->subDay()->toDateString();
        }

        $streak = 0;
        while ($completedDates->contains($cursor)) {
            $streak++;
            $cursor = Carbon::parse($cursor)->subDay()->toDateString();
        }

        return $streak;
    }

    /**
     * Последние 7 дней (сегодня — последний) с отметкой, был ли в этот
     * день завершён хотя бы один урок — для полоски дней streak на дашборде.
     *
     * @return array<int, array{label: string, active: bool, isToday: bool}>
     */
    private function lastSevenDays(User $user): array
    {
        $completedDates = $user->progress()
            ->whereNotNull('completed_at')
            ->pluck('completed_at')
            ->map(fn ($date) => $date->toDateString())
            ->unique();

        $days = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $days[] = [
                'label' => $date->translatedFormat('D'),
                'active' => $completedDates->contains($date->toDateString()),
                'isToday' => $i === 0,
            ];
        }

        return $days;
    }
}
