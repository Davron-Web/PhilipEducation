<?php

namespace Database\Seeders;

use App\Models\Test\TestQuestion;
use Illuminate\Database\Seeder;

class TestQuestionSeeder extends Seeder
{
    public function run(): void
    {
        TestQuestion::insert([
            [
                'test_id' => 1,
                'question' => 'Hello = ?',
                'type' => 'multiple_choice',
                'points' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
