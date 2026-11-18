<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Закреплённый язык бота в общем чате — 'tg' или 'ru' (код 'tg', не
     * 'tj': так уже во всей остальной локализации проекта, lang/tg/*.php,
     * App\Http\Middleware\SetLocale::SUPPORTED, config/chat_bots.php).
     * У обычных пользователей остаётся null — у них язык интерфейса это
     * session('locale'), отдельная, никак не связанная с этим полем вещь.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('lang', 2)->nullable()->after('is_bot');
            $table->index('lang');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['lang']);
            $table->dropColumn('lang');
        });
    }
};
