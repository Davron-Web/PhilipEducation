<?php

namespace Database\Seeders;

use App\Models\Content\Lesson;
use Illuminate\Database\Seeder;

/**
 * Исходная заготовка из двух уроков, с которой начинался проект — до
 * BulkLessonSeeder и его 100 полных уроков. Оставлена как есть (правки
 * миграций/сидеров, уже отработавших на боевой базе, не переигрываем),
 * но помечена черновиком: LessonContentSeeder даёт ей одну строку текста
 * без упражнений и теста, и в реальной базе эти два урока давно заменены.
 */
class LessonSeeder extends Seeder
{
    public function run(): void
    {
        Lesson::insert([
            [
                'title' => 'Greetings',
                'level_id' => 1,
                'order_number' => 1,
                // Черновая заготовка (см. класс-докблок ниже): 100 полноценных
                // уроков даёт BulkLessonSeeder, эти два не публикуем, чтобы
                // не показывать студенту "урок" без упражнений и теста.
                'is_published' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Family',
                'level_id' => 1,
                'order_number' => 2,
                // Черновая заготовка (см. класс-докблок ниже): 100 полноценных
                // уроков даёт BulkLessonSeeder, эти два не публикуем, чтобы
                // не показывать студенту "урок" без упражнений и теста.
                'is_published' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
