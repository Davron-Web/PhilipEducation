<?php

namespace Database\Seeders;

use App\Models\Gamification\Achievement;
use Illuminate\Database\Seeder;

/**
 * Заполняет то, что осталось пустым у уже существующих 8 достижений:
 *
 * 1. icon — было NULL у всех восьми (страница /achievements рисовала всем
 *    одну и ту же иконку замка/трофея независимо от типа).
 * 2. code/condition_type/condition_value — у четырёх старых записей
 *    (First Lesson, 5/10 Lessons Completed, Persistent Learner) были NULL,
 *    и AchievementService::checkAndAward() их вообще не проверял: не было
 *    ни одной ветки match() на уроки или серию дней. Теперь ветки
 *    добавлены (lessons_completed, streak_days), и эти четыре достижения
 *    наконец могут реально выдаваться.
 *
 * Ключ идентификации — title: у этих четырёх записей своего code не было,
 * а title в текущей БД уникален для всех восьми (проверено выборкой перед
 * написанием сидера). Идемпотентно — update() по title, безопасно
 * перезапускать.
 */
class AchievementBackfillSeeder extends Seeder
{
    public function run(): void
    {
        $updates = [
            'First Lesson' => [
                'code' => 'first_lesson',
                'icon' => 'lesson',
                'condition_type' => 'lessons_completed',
                'condition_value' => 1,
            ],
            '5 Lessons Completed' => [
                'code' => 'lessons_completed_5',
                'icon' => 'lesson',
                'condition_type' => 'lessons_completed',
                'condition_value' => 5,
            ],
            '10 Lessons Completed' => [
                'code' => 'lessons_completed_10',
                'icon' => 'lesson',
                'condition_type' => 'lessons_completed',
                'condition_value' => 10,
            ],
            // Порог не был нигде задан заранее — 7 дней (неделя) выбран как
            // общепринятая для геймификации первая веха серии; при желании
            // легко поменять здесь одним числом.
            'Persistent Learner' => [
                'code' => 'streak_7_days',
                'icon' => 'streak',
                'condition_type' => 'streak_days',
                'condition_value' => 7,
            ],
            'First Test Passed' => [
                'icon' => 'test',
            ],
            'Первые выражения' => [
                'icon' => 'expression',
            ],
            'Мастер выражений' => [
                'icon' => 'expression',
            ],
            'Мастер слов' => [
                'icon' => 'word',
            ],
        ];

        foreach ($updates as $title => $data) {
            Achievement::where('title', $title)->update($data);
        }
    }
}
