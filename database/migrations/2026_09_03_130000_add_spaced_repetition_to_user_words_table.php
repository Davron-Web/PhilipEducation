<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Поля для интервального повторения. correct_answers/wrong_answers и
 * last_reviewed_at в таблице уже были — не хватало именно расписания:
 * когда слово показать снова и с каким интервалом.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_words', function (Blueprint $table) {
            $table->unsignedInteger('repetitions')->default(0)->after('wrong_answers');
            $table->unsignedInteger('interval_days')->default(0)->after('repetitions');
            // Коэффициент лёгкости из SM-2: чем чаще ошибаемся, тем он меньше
            // и тем плотнее показываем слово.
            $table->decimal('ease_factor', 4, 2)->default(2.50)->after('interval_days');
            $table->timestamp('next_review_at')->nullable()->after('ease_factor');

            $table->index(['user_id', 'next_review_at']);
        });
    }

    public function down(): void
    {
        Schema::table('user_words', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'next_review_at']);
            $table->dropColumn(['repetitions', 'interval_days', 'ease_factor', 'next_review_at']);
        });
    }
};
