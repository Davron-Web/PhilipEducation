<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dialogues', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            // Ключ категории ('greetings', 'restaurant', ...) — подписи
            // берутся из lang/*/site.php (dialogues.categories.*), как и
            // subcategory у Expression (site.expressions.linking_subgroups.*).
            $table->string('category')->nullable();
            $table->foreignId('level_id')->nullable()->constrained('levels')->nullOnDelete();
            $table->string('title_en');
            $table->string('title_ru');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dialogues');
    }
};
