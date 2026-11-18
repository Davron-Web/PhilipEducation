<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Модель теперь может показывать диапазон из двух соседних уровней
     * вместо одного (см. ml/src/range_rule.py) — когда вероятность
     * соседнего уровня достаточно близка к лучшей, показывать один
     * уровень было бы ложной точностью. Только добавление колонок —
     * старые записи (без диапазона, sentence_count) как были, так и
     * остаются валидными: все три поля nullable, ничего не удаляем и не
     * делаем NOT NULL задним числом.
     */
    public function up(): void
    {
        Schema::table('level_checks', function (Blueprint $table) {
            $table->foreignId('range_low_level_id')->nullable()->after('predicted_level_id')->constrained('levels');
            $table->foreignId('range_high_level_id')->nullable()->after('range_low_level_id')->constrained('levels');
            // Сколько предложений распознал сплиттер (ml/src/sentence_split.py)
            // — для пометки «оценка по короткому тексту менее точна» при < 3.
            // Nullable: у старых записей текст скармливался модели одним
            // куском без подсчёта предложений вовсе.
            $table->unsignedSmallInteger('sentence_count')->nullable()->after('probabilities');
            // Разбор по предложениям (текст + уровень каждого) — для
            // свёрнутого блока на странице результата. Отдельная колонка,
            // не вложено в probabilities: тот столбец уже используется как
            // плоский {код_уровня: вероятность} по всему result.blade.php
            // (see $levelCheck->probabilities[$code]), мешать туда ещё
            // один формат — значит либо сломать это чтение, либо гадать
            // на каждой странице, что перед нами. Nullable — у старых
            // записей и у ответа сервиса без ключа per_sentence будет null.
            $table->json('per_sentence')->nullable()->after('sentence_count');
        });
    }

    public function down(): void
    {
        Schema::table('level_checks', function (Blueprint $table) {
            $table->dropConstrainedForeignId('range_low_level_id');
            $table->dropConstrainedForeignId('range_high_level_id');
            $table->dropColumn(['sentence_count', 'per_sentence']);
        });
    }
};
