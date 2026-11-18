<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Рейтинг переиспользует уже существующие users.points/xp_events (см.
     * XpService) вместо отдельной таблицы очков — здесь только то, чего для
     * рейтинга не хватало: приватность и индекс под сортировку по очкам.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('show_in_leaderboard')->default(true)->after('chat_banned_until');

            // Все запросы рейтинга за всё время фильтруют по видимости и
            // сразу сортируют по очкам по убыванию — составной индекс
            // покрывает оба условия одним проходом.
            $table->index(['show_in_leaderboard', 'points']);
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['show_in_leaderboard', 'points']);
            $table->dropColumn('show_in_leaderboard');
        });
    }
};
