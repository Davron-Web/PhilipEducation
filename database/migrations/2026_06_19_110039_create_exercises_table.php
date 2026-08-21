<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Создание таблицы exercises
     */
    public function up(): void
    {
        Schema::create('exercises', function (Blueprint $table) {

            $table->id();

            // Связь с таблицей lessons
            $table->foreignId('lesson_id')
                ->constrained('lessons')
                ->cascadeOnDelete();

            $table->string('title');

            $table->enum('type', [
                'fill_blank',
                'matching',
                'listening',
                'speaking',
                'translation'
            ]);

            $table->longText('instructions');

            $table->timestamps();
        });
    }

    /**
     * Удаление таблицы exercises
     */
    public function down(): void
    {
        Schema::dropIfExists('exercises');
    }
};
