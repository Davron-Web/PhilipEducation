<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Незавершённый тест: что ученик успел отметить, но не отправил.
     *
     * Отдельная таблица, а не запись в test_attempts с пустым finished_at:
     * попытка — это результат, она участвует в статистике и в подсчёте
     * сданных тестов. Черновик результатом не является и должен исчезать
     * бесследно, когда тест отправлен.
     *
     * Один черновик на пару «ученик + тест»: вернувшись, человек продолжает
     * с того места, где остановился, а не выбирает из нескольких копий.
     */
    public function up(): void
    {
        Schema::create('test_drafts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('test_id')->constrained()->cascadeOnDelete();
            $table->json('answers');
            $table->unsignedInteger('seconds_spent')->default(0);
            $table->timestamps();

            $table->unique(['user_id', 'test_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('test_drafts');
    }
};
