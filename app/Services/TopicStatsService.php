<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Статистика ошибок ученика по темам.
 *
 * Считается запросом с агрегацией, а не перебором ответов в PHP: у активного
 * ученика их сотни, и загружать их все ради подсчёта долей незачем.
 */
class TopicStatsService
{
    /** Ниже этой доли верных ответов тема считается слабой. */
    public const WEAK_THRESHOLD = 0.6;

    /** Меньше этого числа ответов — судить о теме рано. */
    public const MIN_ANSWERS = 3;

    /**
     * @return Collection<int, array{topic: string, total: int, correct: int, wrong: int, accuracy: float, is_weak: bool}>
     */
    public function forUser(User $user): Collection
    {
        $rows = DB::table('user_answers')
            ->join('test_attempts', 'test_attempts.id', '=', 'user_answers.attempt_id')
            ->join('test_questions', 'test_questions.id', '=', 'user_answers.question_id')
            ->where('test_attempts.user_id', $user->id)
            ->whereNotNull('test_questions.topic')
            ->groupBy('test_questions.topic')
            ->select([
                'test_questions.topic',
                DB::raw('count(*) as total'),
                DB::raw('sum(user_answers.is_correct) as correct'),
            ])
            ->get();

        return $rows
            ->map(function ($row) {
                $total = (int) $row->total;
                $correct = (int) $row->correct;
                $accuracy = $total > 0 ? $correct / $total : 0.0;

                return [
                    'topic' => $row->topic,
                    'total' => $total,
                    'correct' => $correct,
                    'wrong' => $total - $correct,
                    'accuracy' => round($accuracy, 4),
                    // Одна ошибка из двух ответов — ещё не слабая тема,
                    // поэтому нужен минимум ответов.
                    'is_weak' => $total >= self::MIN_ANSWERS && $accuracy < self::WEAK_THRESHOLD,
                ];
            })
            ->sortBy([['is_weak', 'desc'], ['accuracy', 'asc']])
            ->values();
    }

    /** @return Collection<int, array{topic: string, total: int, correct: int, wrong: int, accuracy: float, is_weak: bool}> */
    public function weakTopics(User $user, int $limit = 5): Collection
    {
        return $this->forUser($user)
            ->filter(fn (array $row) => $row['is_weak'])
            ->take($limit)
            ->values();
    }

    /** Человеческое название темы: present-simple → Present Simple. */
    public function label(string $topic): string
    {
        return ucwords(str_replace('-', ' ', $topic));
    }
}
