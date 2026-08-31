<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ielts_passages', function (Blueprint $table) {
            $table->id();
            // reading — текст читается глазами; listening — тот же формат,
            // но текст озвучивается через speechSynthesis и скрыт до конца.
            $table->enum('skill', ['reading', 'listening']);
            $table->string('title');
            $table->string('level')->nullable();
            $table->longText('passage_text');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ielts_passages');
    }
};
