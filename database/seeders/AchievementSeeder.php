<?php

namespace Database\Seeders;

use App\Models\Gamification\Achievement;
use Illuminate\Database\Seeder;

class AchievementSeeder extends Seeder
{
    public function run(): void
    {
        Achievement::insert([
            ['title' => 'First Lesson', 'points' => 10, 'created_at' => now(), 'updated_at' => now()],
            ['title' => 'Words Master', 'points' => 20, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
