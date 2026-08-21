<?php

namespace Database\Seeders;

use App\Models\Content\Lesson;
use Illuminate\Database\Seeder;

class LessonSeeder extends Seeder
{
    public function run(): void
    {
        Lesson::insert([
            [
                'title' => 'Greetings',
                'level_id' => 1,
                'order_number' => 1,
                'is_published' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Family',
                'level_id' => 1,
                'order_number' => 2,
                'is_published' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
