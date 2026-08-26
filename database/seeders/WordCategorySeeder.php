<?php

namespace Database\Seeders;

use App\Models\Vocabulary\Word;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Распределяет слова по тематическим категориям (см.
 * database/seeders/data/word_categories.json — сопоставление получено
 * вручную для всего словаря сайта, ключи — слово в нижнем регистре).
 * Обновляет все строки с таким словом (одно и то же слово может
 * повторяться в нескольких уроках), поэтому безопасно перезапускать.
 */
class WordCategorySeeder extends Seeder
{
    public function run(): void
    {
        $path = database_path('seeders/data/word_categories.json');
        $map = json_decode(file_get_contents($path), true);

        $updated = 0;
        $unmatched = [];

        foreach ($map as $word => $category) {
            $affected = Word::whereRaw('LOWER(word) = ?', [Str::lower($word)])
                ->update(['category' => $category]);

            if ($affected === 0) {
                $unmatched[] = $word;
            }
            $updated += $affected;
        }

        $this->command?->info("WordCategorySeeder: updated {$updated} word rows across ".count($map).' known words.');

        if ($unmatched) {
            $this->command?->warn('WordCategorySeeder: no matching row for: '.implode(', ', $unmatched));
        }
    }
}
