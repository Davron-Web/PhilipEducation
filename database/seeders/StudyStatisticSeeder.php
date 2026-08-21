<?php

namespace Database\Seeders;

use App\Models\Gamification\StudyStatistic;
use Illuminate\Database\Seeder;

class StudyStatisticSeeder extends Seeder
{
    public function run(): void
    {
        StudyStatistic::insert([
            [
                'user_id' => 1,
                'total_lessons_completed' => 0,
                'total_tests_passed' => 0,
                'total_words_learned' => 0,
                'study_time_minutes' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
