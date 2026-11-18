<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Флаг фейковых пользователей-ботов, наполняющих общий чат (см.
     * UserFactory::bot(), ChatBotSeeder, команда chat:bots). Отдельная
     * колонка, а не эвристика по email/имени — чтобы отличать ботов от
     * живых людей можно было одним простым запросом (для админки,
     * статистики, будущей модерации).
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_bot')->default(false)->after('show_in_leaderboard');
            $table->index('is_bot');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['is_bot']);
            $table->dropColumn('is_bot');
        });
    }
};
