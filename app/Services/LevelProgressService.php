<?php

namespace App\Services;

use App\Models\Content\Lesson;
use App\Models\User;
use App\Notifications\LevelCompleted;

/**
 * Отслеживает закрытие уровня целиком.
 *
 * Курсов в проекте нет — материал разложен по уровням CEFR, поэтому
 * «завершение курса» из требований здесь означает пройденные уроки
 * одного уровня.
 */
class LevelProgressService
{
    /**
     * Проверяет, не стал ли этот урок последним незакрытым на своём уровне,
     * и если да — поздравляет ученика.
     */
    public function checkAfterLesson(User $user, Lesson $lesson): void
    {
        if (! $lesson->level_id) {
            return;
        }

        $lessonIds = Lesson::where('is_published', true)
            ->where('level_id', $lesson->level_id)
            ->pluck('id');

        if ($lessonIds->isEmpty()) {
            return;
        }

        $completed = $user->progress()
            ->whereIn('lesson_id', $lessonIds)
            ->where('is_completed', true)
            ->count();

        if ($completed < $lessonIds->count()) {
            return;
        }

        // Уровень мог быть закрыт раньше: пройденный урок иногда открывают
        // повторно. Второе поздравление за то же самое выглядит сбоем.
        $levelName = $lesson->level?->name ?? $lesson->level?->code ?? 'без названия';
        $title = 'Уровень '.$levelName.' пройден';

        if ($user->notifications()->where('title', $title)->exists()) {
            return;
        }

        $user->notify(new LevelCompleted($levelName, $lessonIds->count()));
    }
}
