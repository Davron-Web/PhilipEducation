<?php

namespace Database\Seeders;

use App\Models\Vocabulary\Word;
use Illuminate\Database\Seeder;

class WordSeeder extends Seeder
{
    public function run(): void
    {
        Word::insert([
            [
                'lesson_id' => 1,
                'word' => 'hello',
                'transcription' => 'həˈloʊ',
                'example' => 'Hello, how are you?',
                'difficulty' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'lesson_id' => 1,
                'word' => 'bye',
                'transcription' => 'baɪ',
                'example' => 'Bye, see you tomorrow!',
                'difficulty' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
