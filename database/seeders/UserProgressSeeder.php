<?php

namespace Database\Seeders;

use App\Models\User\UserProgress;
use Illuminate\Database\Seeder;

class UserProgressSeeder extends Seeder
{
    public function run(): void
    {
        UserProgress::insert([
            [
                'user_id' => 1,
                'lesson_id' => 1,
                'is_completed' => false,
                'progress_percent' => 30,
                'time_spent' => 0,
                'last_position' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
