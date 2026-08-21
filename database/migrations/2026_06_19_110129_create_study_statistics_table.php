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
        Schema::create('study_statistics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();

            $table->integer('total_lessons_completed')->default(0);
            $table->integer('total_tests_passed')->default(0);
            $table->integer('total_words_learned')->default(0);
            $table->integer('study_time_minutes')->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('study_statistics');
    }
};
