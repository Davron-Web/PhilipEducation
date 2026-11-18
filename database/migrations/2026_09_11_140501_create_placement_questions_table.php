<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Банк вопросов теста на определение уровня. Каждый вопрос откалиброван
 * под конкретный уровень (level_id) — по точности ответов на вопросы
 * каждого уровня и определяется итоговый результат, см.
 * PlacementGradingService.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('placement_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('level_id')->constrained();
            $table->text('question');
            $table->unsignedInteger('order_number')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('placement_questions');
    }
};
