<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Переносит существующие words.category (37 уникальных строк на 2468 слов,
 * 306 слов без category) в коллекции типа topic — данные не теряются,
 * колонка category намеренно остаётся в схеме (уберём отдельным шагом
 * позже). Живёт в миграции, а не в сидере, чтобы отработать одинаково в
 * любом окружении (включая прод) при обычном `php artisan migrate`.
 *
 * title_en/title_tg сознательно оставляем NULL здесь — переводы 37 тем
 * заводятся следующей миграцией (см. add_en_tg_titles_to_topic_collections),
 * чтобы сам перенос данных не зависел от качества перевода.
 */
return new class extends Migration
{
    public function up(): void
    {
        $categories = DB::table('words')
            ->whereNotNull('category')
            ->where('category', '!=', '')
            ->distinct()
            ->pluck('category');

        foreach ($categories as $category) {
            $collectionId = DB::table('collections')->insertGetId([
                'type' => 'topic',
                'slug' => Str::slug($category),
                'title_ru' => $category,
                'sort_order' => 0,
                'is_published' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $wordIds = DB::table('words')->where('category', $category)->pluck('id');

            $rows = $wordIds->map(fn ($wordId, $index) => [
                'collection_id' => $collectionId,
                'word_id' => $wordId,
                'sort_order' => $index,
                'created_at' => now(),
                'updated_at' => now(),
            ])->all();

            foreach (array_chunk($rows, 500) as $chunk) {
                DB::table('collection_word')->insert($chunk);
            }
        }
    }

    public function down(): void
    {
        DB::table('collections')->where('type', 'topic')->delete();
        // collection_word строки уйдут каскадом по FK на collection_id.
    }
};
