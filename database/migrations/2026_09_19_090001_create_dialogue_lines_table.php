<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dialogue_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dialogue_id')->constrained('dialogues')->cascadeOnDelete();
            $table->integer('order_number');
            // 'A' / 'B' — какая из двух реплик в диалоге, plus имя говорящего
            // отдельно (одно и то же имя на все реплики этой стороны).
            $table->string('speaker', 1);
            $table->string('name');
            $table->text('text_en');
            $table->text('text_ru');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dialogue_lines');
    }
};
