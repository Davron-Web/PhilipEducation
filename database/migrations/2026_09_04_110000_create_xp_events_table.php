<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Журнал начислений XP.
     *
     * users.points остаётся кэшем суммы — по нему строятся списки и сортировки,
     * а журнал отвечает на вопрос «за что начислено» и не даёт начислить дважды
     * за одно и то же действие: пара (source, source_id) уникальна для человека.
     */
    public function up(): void
    {
        Schema::create('xp_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('source', 40);
            $table->unsignedBigInteger('source_id')->nullable();
            $table->unsignedInteger('amount');
            $table->timestamps();

            $table->unique(['user_id', 'source', 'source_id']);
            // Лента «последние начисления» и сумма за период.
            $table->index(['user_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('xp_events');
    }
};
