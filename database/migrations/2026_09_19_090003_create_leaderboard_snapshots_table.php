<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ежедневный снимок мест в рейтинге. LeaderboardService раньше сравнивал
     * текущее место со «сдвинутым окном» xp_events, но это работает только
     * пока в журнале уже накопилась история нужной длины (14+/30+/60+ дней) —
     * для новых участников и коротких периодов данных ещё нет, поэтому
     * стрелка изменения места была пустой почти у всех. Снимок хранит
     * реальное вчерашнее место каждого пользователя, поэтому «предыдущее
     * место» существует у всех, у кого была хотя бы одна попытка снятия.
     */
    public function up(): void
    {
        Schema::create('leaderboard_snapshots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('period', 10);
            $table->unsignedInteger('rank');
            $table->unsignedInteger('points');
            $table->date('snapshot_date');
            $table->timestamps();

            // Один снимок на пользователя/период/день — команда снапшота
            // идемпотентна (upsert), повторный запуск в тот же день не плодит дубли.
            $table->unique(['user_id', 'period', 'snapshot_date']);

            // Поиск «последнего снимка до сегодня» для конкретного периода.
            $table->index(['period', 'snapshot_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leaderboard_snapshots');
    }
};
