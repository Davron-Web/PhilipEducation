<?php

namespace App\Services;

use App\Models\Content\Lesson;
use App\Models\System\Level;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Карта прогресса: сколько пройдено на каждом уровне и где слабые места.
 *
 * Считает двумя запросами с группировкой независимо от числа уровней и
 * уроков: перебирать уроки в PHP значило бы тянуть все сто с лишним записей
 * ради подсчёта долей.
 */
class ProgressMapService
{
    public function __construct(private readonly TopicStatsService $topicStats) {}

    /**
     * @return Collection<int, array{level: Level, total: int, completed: int, in_progress: int, percent: int}>
     */
    public function levels(User $user): Collection
    {
        // Сколько опубликованных уроков на каждом уровне.
        $totals = Lesson::where('is_published', true)
            ->whereNotNull('level_id')
            ->groupBy('level_id')
            ->select('level_id', DB::raw('count(*) as total'))
            ->pluck('total', 'level_id');

        // Сколько из них пройдено и сколько начато этим учеником.
        $mine = DB::table('user_progress')
            ->join('lessons', 'lessons.id', '=', 'user_progress.lesson_id')
            ->where('user_progress.user_id', $user->id)
            ->where('lessons.is_published', true)
            ->whereNotNull('lessons.level_id')
            ->groupBy('lessons.level_id')
            ->select([
                'lessons.level_id',
                DB::raw('sum(user_progress.is_completed = 1) as completed'),
                DB::raw('sum(user_progress.is_completed = 0) as in_progress'),
            ])
            ->get()
            ->keyBy('level_id');

        return Level::whereIn('id', $totals->keys())
            ->orderBy('code')
            ->get()
            ->map(function (Level $level) use ($totals, $mine) {
                $total = (int) $totals->get($level->id, 0);
                $row = $mine->get($level->id);
                $completed = (int) ($row->completed ?? 0);

                return [
                    'level' => $level,
                    'total' => $total,
                    'completed' => $completed,
                    'in_progress' => (int) ($row->in_progress ?? 0),
                    'percent' => $total > 0 ? (int) round($completed / $total * 100) : 0,
                ];
            })
            ->values();
    }

    /** Темы, где ученик чаще ошибается. */
    public function weakTopics(User $user, int $limit = 6): Collection
    {
        return $this->topicStats->weakTopics($user, $limit);
    }

    /**
     * Темы, которые освоены уверенно — их полезно показать рядом со слабыми,
     * иначе карта выглядит как перечень одних неудач.
     *
     * @return Collection<int, array<string, mixed>>
     */
    public function strongTopics(User $user, int $limit = 6): Collection
    {
        return $this->topicStats->forUser($user)
            ->filter(fn (array $row) => ! $row['is_weak']
                && $row['total'] >= TopicStatsService::MIN_ANSWERS
                && $row['accuracy'] >= 0.8)
            ->sortByDesc('accuracy')
            ->take($limit)
            ->values();
    }
}
