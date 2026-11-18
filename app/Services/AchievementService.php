<?php

namespace App\Services;

use App\Models\Gamification\Achievement;
use App\Models\User;
use App\Notifications\AchievementEarned;

class AchievementService
{
    public function __construct(private readonly XpService $xp) {}

    /**
     * Award every not-yet-earned achievement whose threshold for the given
     * condition type is now met by the user, based on their current learned
     * count for that type (words or expressions).
     */
    public function checkAndAward(User $user, string $conditionType): void
    {
        $count = $this->currentCountFor($user, $conditionType);

        if ($count === 0) {
            return;
        }

        $earnedIds = $user->achievements()->pluck('achievements.id');

        $qualifying = Achievement::where('condition_type', $conditionType)
            ->where('condition_value', '<=', $count)
            ->whereNotIn('id', $earnedIds)
            ->get();

        foreach ($qualifying as $achievement) {
            $user->achievements()->attach($achievement->id, ['earned_at' => now()]);

            // Достижение само по себе даёт опыт — иначе колонка points
            // у достижений оставалась бы просто числом на карточке.
            $this->xp->award($user, 'achievement', $achievement->id, max(1, (int) $achievement->points));
            $user->notify(new AchievementEarned($achievement));
        }
    }

    /**
     * Текущее значение пользователя по типу условия — вынесено отдельно от
     * checkAndAward(), чтобы страница /achievements могла показать честный
     * прогресс-бар («12 из 20 уроков») той же цифрой, что решает, выдавать
     * ли достижение, а не считать её ещё раз по-своему.
     */
    public function currentCountFor(User $user, string $conditionType): int
    {
        return match ($conditionType) {
            'words_learned' => $user->words()->wherePivot('learned', true)->count(),
            'expressions_learned' => $user->expressions()->wherePivot('learned', true)->count(),
            // Считаем разные сданные тесты, а не число попыток: иначе одну
            // и ту же лёгкую проверку можно было бы пройти пять раз подряд.
            'tests_passed' => $user->results()->where('passed', true)->distinct()->count('test_id'),
            // Тот же счётчик, что и на дашборде/в профиле (User::completedLessonsCount()),
            // и та же серия дней, что в шапке дашборда (User::currentStreak()) —
            // не пересчитываем условие по-своему, иначе цифра на карточке
            // достижения могла бы разойтись с тем, что видно на дашборде.
            'lessons_completed' => $user->completedLessonsCount(),
            'streak_days' => $user->currentStreak(),
            default => 0,
        };
    }
}
