<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Видеоуроки, прикреплённые к уроку.
     *
     * Отдельная таблица, а не колонка video_url: к одной теме обычно
     * находится не одно видео — объяснение, разбор примеров, тренировка
     * произношения, — и порядок показа важен.
     *
     * Храним исходную ссылку как её дал администратор, а адрес для плеера
     * собираем в модели: так в базе остаётся то, что можно открыть руками
     * и проверить, а формат встраивания можно менять без миграции.
     */
    public function up(): void
    {
        Schema::create('lesson_videos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lesson_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->string('url');
            $table->string('description')->nullable();
            $table->unsignedSmallInteger('duration_minutes')->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->boolean('is_published')->default(true);
            $table->timestamps();

            $table->index(['lesson_id', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lesson_videos');
    }
};
