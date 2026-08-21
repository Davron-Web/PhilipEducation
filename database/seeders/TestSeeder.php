<?php

namespace Database\Seeders;

use App\Models\Test\Test;
use Illuminate\Database\Seeder;

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
                'is_published' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
