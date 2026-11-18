<?php

namespace App\Http\Controllers\Public\Gamification;

use App\Http\Controllers\Controller;
use App\Models\Gamification\Achievement;
use App\Models\User;
use App\Services\AchievementService;
use App\Services\CertificateService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class AchievementController extends Controller
{
    public function index(AchievementService $achievements, CertificateService $certificates): View
    {
        $user = Auth::user();
        $earned = $user->achievements()->get()->keyBy('id');

        $list = Achievement::orderByDesc('points')->get()->map(function (Achievement $achievement) use ($earned, $user, $achievements) {
            $isEarned = (bool) $earned->get($achievement->id);
            $progress = null;

            // Прогресс только для незаработанных и только если условие
            // реально измеримо (у всех восьми оно теперь есть, но код не
            // падает, если у будущего достижения его не окажется).
            if (! $isEarned && $achievement->condition_type && $achievement->condition_value) {
                $current = $achievements->currentCountFor($user, $achievement->condition_type);

                $progress = [
                    'current' => min($current, $achievement->condition_value),
                    'target' => $achievement->condition_value,
                    'percent' => min(100, (int) round(($current / $achievement->condition_value) * 100)),
                    'label' => $this->progressLabel($achievement->condition_type),
                ];
            }

            return [
                'achievement' => $achievement,
                'is_earned' => $isEarned,
                'earned_at' => $isEarned ? $earned->get($achievement->id)->pivot->earned_at : null,
                'progress' => $progress,
            ];
        });

        $heatmap = $this->activityHeatmap($user);
        $certificateLevels = $certificates->progressFor($user);

        // Имя на превью сертификата — то же самое для полученных и ещё не
        // полученных (человек должен видеть СВОЙ будущий сертификат, а не
        // заглушку): латиница из профиля, если её вписали, иначе обычное
        // name как есть (пусть даже кириллицей). Подсказка «впишите
        // латиницей» показывается только когда есть реальная причина —
        // кириллица в имени и пустое name_latin, а не просто «на всякий
        // случай» для всех подряд.
        $certificateName = filled($user->name_latin) ? $user->name_latin : $user->name;
        $certificateNeedsLatinHint = blank($user->name_latin) && (bool) preg_match('/\p{Cyrillic}/u', $user->name ?? '');

        return view('public.achievements.index', [
            'achievements' => $list,
            'earnedCount' => $earned->count(),
            'totalCount' => $list->count(),
            'heatmap' => $heatmap,
            'certificateLevels' => $certificateLevels,
            'certificateName' => $certificateName,
            'certificateNeedsLatinHint' => $certificateNeedsLatinHint,
        ]);
    }

    /** Подпись под прогресс-баром — «12 из 20 уроков» и т.п. */
    private function progressLabel(string $conditionType): string
    {
        return match ($conditionType) {
            'lessons_completed' => 'уроков',
            'words_learned' => 'слов',
            'expressions_learned' => 'выражений',
            'tests_passed' => 'тестов',
            'streak_days' => 'дней подряд',
            default => '',
        };
    }

    /**
     * Активность за 12 недель (84 дня, сегодня — последний) по количеству
     * XP за день — реальные данные из XpEvent, дни без начислений идут
     * с xp=0, а не пропускаются. Плоский список, а не недели по
     * календарным Вс–Сб: 84 = 12×7 ровно, поэтому 12 колонок по 7 дней
     * получаются без обрезанных «хвостов» на первой/последней неделе —
     * ценой того, что колонки не совпадают с настоящими календарными
     * неделями (ряд не всегда «среда»), поэтому подписи дней недели/
     * месяцев не рисуем — были бы нечестными.
     *
     * streak/total_xp/active_days — все три посчитаны из ТОГО ЖЕ прохода
     * по дням, ничего не выдумано и не запрошено отдельно: streak — уже
     * существующий User::currentStreak(), totalXp и activeDays — сумма и
     * count(xp>0) по тому же массиву cells.
     *
     * @return array{cells: array<int, array{date: string, xp: int, level: int}>, streak: int, total_xp: int, active_days: int}
     */
    private function activityHeatmap(User $user): array
    {
        $days = 84;
        $start = Carbon::today()->subDays($days - 1);

        $xpByDay = $user->xpEvents()
            ->where('created_at', '>=', $start->copy()->startOfDay())
            ->get()
            ->groupBy(fn ($event) => $event->created_at->toDateString())
            ->map(fn ($events) => $events->sum('amount'));

        $cells = [];
        $totalXp = 0;
        $activeDays = 0;

        for ($i = 0; $i < $days; $i++) {
            $date = $start->copy()->addDays($i);
            $xp = (int) $xpByDay->get($date->toDateString(), 0);
            $totalXp += $xp;
            if ($xp > 0) {
                $activeDays++;
            }

            $cells[] = [
                'date' => $date->toDateString(),
                'xp' => $xp,
                'level' => match (true) {
                    $xp === 0 => 0,
                    $xp <= 20 => 1,
                    $xp <= 50 => 2,
                    $xp <= 100 => 3,
                    default => 4,
                },
            ];
        }

        return [
            'cells' => $cells,
            'streak' => $user->currentStreak(),
            'total_xp' => $totalXp,
            'active_days' => $activeDays,
        ];
    }
}
