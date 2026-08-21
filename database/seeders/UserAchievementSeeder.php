<?php

namespace Database\Seeders;

use App\Models\User\UserAchievement;
use Illuminate\Database\Seeder;

class UserAchievementSeeder extends Seeder
{
    public function run(): void
    {
        UserAchievement::insert([
            [
                'user_id' => 1,
                'achievement_id' => 1,
                'earned_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
