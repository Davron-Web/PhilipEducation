<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Тема вопроса — present-simple, articles и т.п.
     *
     * Строка-slug, а не связь с grammar_topics: тема нужна и вопросам, не
     * привязанным к разделу грамматики (лексика, предлоги), и должна
     * переживать переименование темы. По ней считается статистика ошибок,
     * поэтому важен индекс.
     */
    public function up(): void
    {
        Schema::table('test_questions', function (Blueprint $table) {
            $table->string('topic', 80)->nullable()->after('question');
            $table->index('topic');
        });
    }

    public function down(): void
    {
        Schema::table('test_questions', function (Blueprint $table) {
            $table->dropIndex(['topic']);
            $table->dropColumn('topic');
        });
    }
};
