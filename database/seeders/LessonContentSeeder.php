<?php

namespace Database\Seeders;

use App\Models\Content\LessonContent;
use Illuminate\Database\Seeder;

class LessonContentSeeder extends Seeder
{
    public function run(): void
    {
        LessonContent::insert([
            [
                'lesson_id' => 1,
                'type' => 'text',
                'title' => 'Hello',
                'content' => 'Hello means greeting',
                'order_number' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
