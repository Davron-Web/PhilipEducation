<?php

namespace App\Http\Controllers\Public\Gamification;

use App\Http\Controllers\Controller;
use App\Models\Gamification\Achievement;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class AchievementController extends Controller
{
    public function index(): View
    {
        $achievements = Achievement::orderByDesc('points')->get();

        $user = Auth::user();
        $earned = $user->achievements()->get()->keyBy('id');

        $heatmap = $this->activityHeatmap($user);

        return view('public.achievements.index', compact('achievements', 'earned', 'heatmap'));
    }

    /**
     * Число завершённых уроков по дням за последние 12 недель (как
     * календарь активности на GitHub). Основано на реальном
     * completed_at из user_progress — без выдуманных данных.
     *
     * @return array{weeks: array<int, array<int, array{date: string, count: int}>>, max: int}
     */
    private function activityHeatmap($user): array
    {
        $days = 84; // 12 недель
        $start = Carbon::today()->subDays($days - 1);

        $counts = $user->progress()
            ->whereNotNull('completed_at')
            ->where('completed_at', '>=', $start)
            ->get()
            ->groupBy(fn ($row) => $row->completed_at->toDateString())
            ->map->count();

        $weeks = [];
        $week = [];
        $max = 1;

        for ($i = 0; $i < $days; $i++) {
            $date = $start->copy()->addDays($i);
            $count = $counts->get($date->toDateString(), 0);
            $max = max($max, $count);

            $week[] = ['date' => $date->toDateString(), 'count' => $count];

            if ($date->isSaturday() || $i === $days - 1) {
                $weeks[] = $week;
                $week = [];
            }
        }

        return ['weeks' => $weeks, 'max' => $max];
    }
}
