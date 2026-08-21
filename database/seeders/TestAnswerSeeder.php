<?php

namespace Database\Seeders;

use App\Models\Test\TestAnswer;
use Illuminate\Database\Seeder;

class TestAnswerSeeder extends Seeder
{
    public function run(): void
    {
        TestAnswer::insert([
            [
                'question_id' => 1,
                'answer' => 'привет',
                'is_correct' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'question_id' => 1,
                'answer' => 'пока',
                'is_correct' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
