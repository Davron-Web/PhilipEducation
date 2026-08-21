<?php

namespace Database\Seeders;

use App\Models\User\UserWord;
use Illuminate\Database\Seeder;

class UserWordSeeder extends Seeder
{
    public function run(): void
    {
        UserWord::insert([
            [
                'user_id' => 1,
                'word_id' => 1,
                'learned' => false,
                'correct_answers' => 1,
                'wrong_answers' => 0,
                'last_reviewed_at' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
