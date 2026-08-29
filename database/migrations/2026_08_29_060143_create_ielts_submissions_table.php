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
        Schema::create('ielts_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('ielts_task_id')->constrained()->cascadeOnDelete();
            $table->longText('answer_text');
            $table->unsignedInteger('word_count')->default(0);
            // Оценка и разбор от Gemini — null, пока запрос ещё не отработал/не удался.
            $table->decimal('band_score', 3, 1)->nullable();
            $table->text('feedback')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ielts_submissions');
    }
};
