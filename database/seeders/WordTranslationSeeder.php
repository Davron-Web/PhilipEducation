<?php

namespace Database\Seeders;

use App\Models\Vocabulary\WordTranslation;
use Illuminate\Database\Seeder;

class WordTranslationSeeder extends Seeder
{
    public function run(): void
    {
        WordTranslation::insert([
            [
                'word_id' => 1,
                'language' => 'ru',
                'translation' => 'привет',
                'definition' => 'Приветствие при встрече',
                'example' => 'Hello, how are you? — Привет, как дела?',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'word_id' => 2,
                'language' => 'ru',
                'translation' => 'пока',
                'definition' => 'Прощание при расставании',
                'example' => 'Bye, see you tomorrow! — Пока, увидимся завтра!',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
