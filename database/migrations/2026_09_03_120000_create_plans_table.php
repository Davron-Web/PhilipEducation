<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();          // weekly | monthly | yearly
            $table->string('name');
            $table->integer('duration_days');
            // Цена в минорных единицах (дирамах), чтобы не терять копейки на float.
            $table->unsignedBigInteger('price_minor');
            $table->string('currency', 3)->default('TJS');
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plans');
    }
};
