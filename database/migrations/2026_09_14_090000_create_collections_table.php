<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('collections', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['level', 'topic', 'exam']);
            $table->string('slug');
            $table->string('title_ru');
            $table->string('title_en')->nullable();
            $table->string('title_tg')->nullable();
            $table->text('description')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_published')->default(true);
            $table->timestamps();

            // composite, не просто unique(slug): exam- и topic-коллекция могут
            // хотеть один и тот же человекочитаемый slug, не мешая друг другу.
            $table->unique(['type', 'slug']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('collections');
    }
};
