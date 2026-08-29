<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('ielts_tasks', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['writing_task1', 'writing_task2']);
            $table->string('title');
            $table->text('prompt');
            // Task 1 only: тип графика (bar/line/pie/table) и данные для отрисовки.
            $table->string('chart_type')->nullable();
            $table->json('chart_data')->nullable();
            // Task 2 only: тематическая категория эссе (Education, Environment...).
            $table->string('topic')->nullable();
            $table->unsignedSmallInteger('min_words')->default(150);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ielts_tasks');
    }
};
