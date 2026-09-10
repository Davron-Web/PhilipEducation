<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('grammar_topics', function (Blueprint $table) {
            // Было ->after('order_number') — такой колонки в grammar_topics
            // не существует ни в одной миграции; на боевой базе она,
            // видимо, правилась вручную в обход миграций, отчего
            // migrate:fresh падал здесь на чистой установке. is_published
            // нигде не используется (админская вьюха и так проверяет её
            // через Schema::hasColumn), so позиция значения не имеет.
            $table->boolean('is_published')->default(true)->after('theory');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Было Schema::table('grammar_t', ...) — опечатка в имени таблицы
        // плюс пустое тело, откат ничего не делал.
        Schema::table('grammar_topics', function (Blueprint $table) {
            $table->dropColumn('is_published');
        });
    }
};
