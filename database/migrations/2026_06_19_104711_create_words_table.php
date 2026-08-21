<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('words', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lesson_id')->nullable()->constrained();
            $table->string('word');
            $table->string('transcription')->nullable();
            $table->text('example')->nullable();
            $table->string('image')->nullable();
            $table->integer('difficulty')->default(1);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('words');
    }
};
