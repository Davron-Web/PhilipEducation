<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * История проверок «Определи свой уровень по тексту» — отдельно от
     * placement_attempts (50-вопросный тест, который официально проставляет
     * users.level_id). Здесь — быстрая неофициальная оценка по тексту в
     * 1-5 предложений через собственную ML-модель (ml/), результат не
     * трогает users.level_id, только копится в истории пользователя.
     */
    public function up(): void
    {
        Schema::create('level_checks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->text('input_text');
            $table->foreignId('predicted_level_id')->constrained('levels');
            // Вероятности по каждому уровню (A1-C2) от модели — для полосок
            // на странице результата; JSON, а не 6 отдельных колонок, т.к.
            // это чисто вывод модели для отображения, не то, по чему будут
            // фильтровать/агрегировать в SQL.
            $table->json('probabilities');
            $table->timestamps();

            $table->index(['user_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('level_checks');
    }
};
