<?php

namespace Database\Seeders;

use App\Models\User\UserResult;
use Illuminate\Database\Seeder;

class UserResultSeeder extends Seeder
{
    public function run(): void
    {
        UserResult::insert([
            [
                'user_id' => 1,
                'test_id' => 1,
                'score' => 1,
                'passed' => true,
                'attempt_date' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
