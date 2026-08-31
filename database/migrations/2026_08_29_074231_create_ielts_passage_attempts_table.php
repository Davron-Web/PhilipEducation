<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ielts_passage_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('ielts_passage_id')->constrained()->cascadeOnDelete();
            // Индекс выбранного варианта на каждый вопрос, по порядку вопросов.
            $table->json('answers');
            $table->unsignedTinyInteger('score');
            $table->unsignedTinyInteger('total');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ielts_passage_attempts');
    }
};
