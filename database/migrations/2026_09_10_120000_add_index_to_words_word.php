<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * WordController::index() сортирует весь словарь по word без индекса —
 * EXPLAIN на 2453 строках показывал type: ALL, Extra: Using filesort.
 *
 * Индекс не ускорит поиск в SearchService (там LIKE '%…%' с ведущим
 * wildcard, префиксный B-tree тут не помогает), но убирает лишнюю
 * сортировку полным сканом на ORDER BY word.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('words', function (Blueprint $table) {
            $table->index('word');
        });
    }

    public function down(): void
    {
        Schema::table('words', function (Blueprint $table) {
            $table->dropIndex(['word']);
        });
    }
};
