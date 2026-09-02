<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('expressions', function (Blueprint $table) {
            $table->id();
            $table->string('text');
            $table->enum('type', [
                'idiom',
                'phrasal_verb',
                'proverb',
                'collocation',
            ]);
            $table->string('transcription')->nullable();
            $table->text('example')->nullable();
            $table->string('audio_url')->nullable();
            $table->boolean('audio_checked')->default(false);
            $table->text('meaning')->nullable();
            $table->string('literal_translation')->nullable();
            $table->integer('difficulty')->default(1);
            $table->string('category')->nullable();
            $table->foreignId('level_id')->nullable()->constrained()->nullOnDelete();
            $table->string('base_verb')->nullable();
            $table->string('particle')->nullable();
            $table->boolean('separable')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('expressions');
    }
};
