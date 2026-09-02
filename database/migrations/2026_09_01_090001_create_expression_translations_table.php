<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('expression_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('expression_id')->constrained()->cascadeOnDelete();
            $table->string('language', 10);
            $table->string('translation');
            $table->text('definition')->nullable();
            $table->text('example')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('expression_translations');
    }
};
