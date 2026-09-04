<?php

namespace App\Services;

use App\Models\Gamification\Title;
use App\Models\User;
use App\Notifications\TitleEarned;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

/**
 * Начисление опыта и выдача титулов.
 *
 * До этого users.points никто не увеличивал: колонка существовала, на неё
 * ссылались профиль и админка, но реальная учёба её не меняла. Здесь опыт
 * становится настоящим — с журналом начислений и защитой от повторов.
 */
class XpService
{
    /** Сколько опыта даёт каждое действие. */
    public const REWARDS = [
        'lesson' => 25,
        'test' => 40,
        'word' => 2,
        'achievement' => 50,
    ];

    /**
     * Начисляет опыт один раз за конкретное действие.
     *
     * $sourceId — id урока, теста, слова: пара (source, source_id) уникальна,
     * поэтому повторное прохождение того же урока опыт не удваивает.
     * Возвращает начисленное количество (0 — если уже начисляли).
     */
    public function award(User $user, string $source, ?int $sourceId = null, ?int $amount = null): int
    {
        $amount ??= self::REWARDS[$source] ?? 0;

        if ($amount <= 0) {
            return 0;
        }

        try {
            DB::transaction(function () use ($user, $source, $sourceId, $amount) {
                $user->xpEvents()->create([
                    'source' => $source,
                    'source_id' => $sourceId,
                    'amount' => $amount,
                ]);

                // increment, а не $user->points + $amount: два одновременных
                // запроса иначе перезаписали бы начисление друг друга.
                $user->increment('points', $amount);
            });
        } catch (QueryException $e) {
            // Нарушение уникального индекса — за это действие уже начислено.
            if ($this->isDuplicate($e)) {
                return 0;
            }

            throw $e;
        }

        $this->syncTitle($user->refresh());

        return $amount;
    }

    /**
     * Выдаёт все титулы, порог которых пройден.
     *
     * Титулы копятся, а «текущим» считается самый старший из полученных —
     * так история званий остаётся видна в профиле.
     */
    public function syncTitle(User $user): void
    {
        $earnedIds = $user->titles()->pluck('titles.id');

        $qualifying = Title::where('is_active', true)
            ->where('min_xp', '<=', $user->points)
            ->whereNotIn('id', $earnedIds)
            ->get();

        foreach ($qualifying as $title) {
            $user->titles()->attach($title->id, ['earned_at' => now()]);
            $user->notify(new TitleEarned($title));
        }
    }

    private function isDuplicate(QueryException $e): bool
    {
        // 23000/23505 — нарушение ограничения целостности в MySQL и SQLite.
        return in_array($e->getCode(), ['23000', '23505'], true);
    }
}
