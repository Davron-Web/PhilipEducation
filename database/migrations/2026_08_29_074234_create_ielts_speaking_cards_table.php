<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ielts_speaking_cards', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('topic')->nullable();
            // Cue card: "Describe a..." + список пунктов, которые нужно раскрыть.
            $table->text('prompt');
            $table->json('cue_points');
            $table->unsignedSmallInteger('prep_seconds')->default(60);
            $table->unsignedSmallInteger('speak_seconds')->default(120);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ielts_speaking_cards');
    }
};
