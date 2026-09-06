<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Порядок вариантов ответа.
     *
     * До этого варианты отдавались в порядке id, а сидер вставлял правильный
     * первым — в 99.7% вопросов ответом был вариант А, и тест проходился без
     * чтения вопросов. Перемешать сами строки нельзя: на их id ссылаются
     * прошлые попытки учеников, и подмена содержимого задним числом исказила
     * бы их результаты. Поэтому порядок задаётся отдельной колонкой.
     */
    public function up(): void
    {
        Schema::table('test_answers', function (Blueprint $table) {
            $table->unsignedSmallInteger('sort_order')->default(0)->after('is_correct');
            $table->index(['question_id', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::table('test_answers', function (Blueprint $table) {
            $table->dropIndex(['question_id', 'sort_order']);
            $table->dropColumn('sort_order');
        });
    }
};
