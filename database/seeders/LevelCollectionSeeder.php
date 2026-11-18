<?php

namespace Database\Seeders;

use App\Models\Vocabulary\Collection;
use App\Models\Vocabulary\Word;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Создаёт 6 коллекций типа level (A1-C2). Наполняются только A1 и A2 —
 * B1-C2 остаются пустыми под будущий контент, как попросил пользователь.
 *
 * Методология отбора A1/A2 (см. план в C:\Users\HP\.claude\plans):
 * кандидаты — слова с уже проставленным в БД Word.difficulty (1 → A1,
 * 2 → A2; шкала реально 1-10, не 1-5, и коррелирует с длиной слова —
 * проверено отдельно). Дальше чистка от выбросов: слова из явно
 * тематических (не «ядровых» бытовых) категорий — религия, космос,
 * компьютеры, техника — исключаются даже при низком difficulty, потому
 * что низкая цифра там объясняется короткостью слова (DJ, USB, Islam), а
 * не тем, что это база для начинающего. Плюс один конкретный мусорный
 * артефакт (word id=1, "wddhh" — очевидно тестовая запись, category
 * «Разное / неопределённые записи»). Слово может повторяться в таблице
 * words по несколько раз (одно и то же слово в разных уроках) — дедуп по
 * тексту без учёта регистра, берём самую раннюю запись, как и публичный
 * WordController::index().
 */
class LevelCollectionSeeder extends Seeder
{
    /** Категории, которые при низком difficulty всё равно не «ядровая» база. */
    private const EXCLUDED_CATEGORIES = [
        'Религия и вера',
        'Космос и астрономия',
        'Компьютеры и интернет',
        'Технологии и техника',
    ];

    /** Единственный известный мусорный артефакт в данных. */
    private const EXCLUDED_WORD_IDS = [1];

    public function run(): void
    {
        $levels = [
            ['code' => 'A1', 'title_ru' => 'Уровень A1', 'title_en' => 'Level A1', 'title_tg' => 'Сатҳи A1', 'difficulty' => 1],
            ['code' => 'A2', 'title_ru' => 'Уровень A2', 'title_en' => 'Level A2', 'title_tg' => 'Сатҳи A2', 'difficulty' => 2],
            ['code' => 'B1', 'title_ru' => 'Уровень B1', 'title_en' => 'Level B1', 'title_tg' => 'Сатҳи B1', 'difficulty' => null],
            ['code' => 'B2', 'title_ru' => 'Уровень B2', 'title_en' => 'Level B2', 'title_tg' => 'Сатҳи B2', 'difficulty' => null],
            ['code' => 'C1', 'title_ru' => 'Уровень C1', 'title_en' => 'Level C1', 'title_tg' => 'Сатҳи C1', 'difficulty' => null],
            ['code' => 'C2', 'title_ru' => 'Уровень C2', 'title_en' => 'Level C2', 'title_tg' => 'Сатҳи C2', 'difficulty' => null],
        ];

        foreach ($levels as $sort => $level) {
            $collection = Collection::updateOrCreate(
                ['type' => 'level', 'slug' => strtolower($level['code'])],
                [
                    'title_ru' => $level['title_ru'],
                    'title_en' => $level['title_en'],
                    'title_tg' => $level['title_tg'],
                    'sort_order' => $sort,
                    'is_published' => true,
                ]
            );

            if ($level['difficulty'] === null) {
                $this->command?->info("LevelCollectionSeeder: {$level['code']} создан пустым (0 слов).");

                continue;
            }

            $wordIds = $this->pickWordIds($level['difficulty']);

            $rows = collect($wordIds)->values()->map(fn ($wordId, $index) => [
                'collection_id' => $collection->id,
                'word_id' => $wordId,
                'sort_order' => $index,
                'created_at' => now(),
                'updated_at' => now(),
            ])->all();

            // Идемпотентно: сначала снимаем старую разметку этого уровня,
            // потом ставим заново — так повторный запуск не плодит дубли и
            // не падает на unique(collection_id, word_id).
            $collection->words()->detach();
            foreach (array_chunk($rows, 500) as $chunk) {
                DB::table('collection_word')->insert($chunk);
            }

            $this->command?->info("LevelCollectionSeeder: {$level['code']} — ".count($wordIds).' слов.');
        }
    }

    /**
     * @return array<int, int> ID слов (по одному на уникальный текст), в
     *                          коротко-раньше сортировке.
     */
    private function pickWordIds(int $difficulty): array
    {
        $candidates = Word::where('difficulty', $difficulty)
            ->whereNotIn('id', self::EXCLUDED_WORD_IDS)
            ->where(function ($query) {
                $query->whereNull('category')->orWhereNotIn('category', self::EXCLUDED_CATEGORIES);
            })
            ->get(['id', 'word']);

        return $candidates
            ->unique(fn ($word) => Str::lower($word->word))
            ->sortBy(fn ($word) => [strlen($word->word), Str::lower($word->word)])
            ->pluck('id')
            ->all();
    }
}
