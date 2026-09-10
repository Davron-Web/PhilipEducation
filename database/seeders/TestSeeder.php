<?php

namespace Database\Seeders;

use App\Models\Test\Test;
use Illuminate\Database\Seeder;

/**
 * Заглушка того же происхождения, что и LessonSeeder (см. его докблок):
 * привязана к lesson_id=1, а этот урок теперь черновик — публиковать тест
 * без опубликованного урока за ним не даёт увидеть студенту нерабочую
 * ссылку.
 */
class TestSeeder extends Seeder
{
    public function run(): void
    {
        Test::insert([
            [
                'lesson_id' => 1,
                'title' => 'Basic Test',
                'passing_score' => 1,
                'time_limit' => 10,
                'is_published' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
