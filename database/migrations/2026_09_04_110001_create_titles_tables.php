<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Титулы — звания, которые ученик получает за накопленный XP.
     *
     * Отдельно от достижений: достижение отмечает разовое событие и остаётся
     * в списке навсегда, титул же один и заменяет предыдущий, поэтому у него
     * есть порядок (min_xp) и понятие «текущий».
     */
    public function up(): void
    {
        Schema::create('titles', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->string('description')->nullable();
            $table->string('icon', 16)->nullable();
            $table->unsignedInteger('min_xp')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('min_xp');
        });

        Schema::create('user_titles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('title_id')->constrained()->cascadeOnDelete();
            $table->timestamp('earned_at')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'title_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_titles');
        Schema::dropIfExists('titles');
    }
};
