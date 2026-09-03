<?php

namespace App\Services;

use App\Models\User;
use App\Models\User\UserWord;
use Illuminate\Database\Eloquent\Collection;

/**
 * Интервальное повторение слов — упрощённый SM-2.
 *
 * Идея: слово, которое ученик уверенно вспомнил, показываем всё реже
 * (1 день → 3 дня → ×коэффициент), а забытое возвращаем в тот же день и
 * начинаем цепочку заново. Так повторения концентрируются на трудных
 * словах, а лёгкие не отнимают время.
 */
class SpacedRepetitionService
{
    /** Границы коэффициента лёгкости — за ними интервалы вырождаются. */
    private const MIN_EASE = 1.3;

    private const MAX_EASE = 2.8;

    /** Сколько слов даём в одной сессии, чтобы она оставалась подъёмной. */
    public const SESSION_SIZE = 20;

    /**
     * Пересчитывает расписание после ответа.
     *
     * @param  bool  $remembered  вспомнил ли ученик слово
     */
    public function review(UserWord $progress, bool $remembered): UserWord
    {
        $ease = $progress->ease_factor ?: 2.5;
        // У только что созданной записи поля пустые — приводим к явным
        // значениям, чтобы дальше не смешивать null и false.
        $progress->learned = (bool) $progress->learned;

        if ($remembered) {
            $progress->correct_answers = ($progress->correct_answers ?? 0) + 1;
            $progress->repetitions = ($progress->repetitions ?? 0) + 1;

            $progress->interval_days = match ($progress->repetitions) {
                1 => 1,
                2 => 3,
                default => max(1, (int) round(($progress->interval_days ?: 3) * $ease)),
            };

            $ease = min(self::MAX_EASE, $ease + 0.1);

            // Слово считается выученным не после первого «знаю», а когда
            // оно продержалось в памяти несколько интервалов подряд.
            if ($progress->repetitions >= 3) {
                $progress->learned = true;
            }
        } else {
            $progress->wrong_answers = ($progress->wrong_answers ?? 0) + 1;
            $progress->repetitions = 0;
            $progress->interval_days = 0;
            $ease = max(self::MIN_EASE, $ease - 0.2);
            // Забытое слово снова требует изучения, иначе счётчик выученных
            // показывал бы то, чего ученик уже не помнит.
            $progress->learned = false;
        }

        $progress->ease_factor = round($ease, 2);
        $progress->last_reviewed_at = now();
        // Интервал 0 означает «вернуть в этой же сессии»: даём 10 минут,
        // чтобы слово не выпало сразу следующей карточкой.
        $progress->next_review_at = $progress->interval_days > 0
            ? now()->addDays($progress->interval_days)->startOfDay()
            : now()->addMinutes(10);

        $progress->save();

        return $progress;
    }

    /**
     * Ставит слово в очередь повторения при первой отметке «знаю».
     * Уже запланированное слово не трогаем — иначе отметка в карточках
     * сбрасывала бы накопленный интервал.
     */
    public function scheduleIfNew(UserWord $progress): UserWord
    {
        if ($progress->next_review_at === null) {
            $progress->repetitions = max(1, $progress->repetitions ?? 0);
            $progress->interval_days = 1;
            $progress->next_review_at = now()->addDay()->startOfDay();
            $progress->save();
        }

        return $progress;
    }

    /** Слова, которые пора повторить. */
    public function dueQueue(User $user, int $limit = self::SESSION_SIZE): Collection
    {
        return UserWord::with('word.translations')
            ->where('user_id', $user->id)
            ->whereNotNull('next_review_at')
            ->where('next_review_at', '<=', now())
            ->orderBy('next_review_at')
            ->limit($limit)
            ->get();
    }

    public function dueCount(User $user): int
    {
        return UserWord::where('user_id', $user->id)
            ->whereNotNull('next_review_at')
            ->where('next_review_at', '<=', now())
            ->count();
    }
}
