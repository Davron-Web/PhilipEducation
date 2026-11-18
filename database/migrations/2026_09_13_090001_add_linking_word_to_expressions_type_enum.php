<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Добавляет 'linking_word' в enum expressions.type. Laravel не умеет менять
 * значения enum-колонки через Schema Builder без doctrine/dbal (которого
 * здесь нет в composer.json), поэтому — сырой ALTER на MySQL (боевая БД),
 * как это уже делает схема проекта в аналогичных случаях.
 *
 * Тесты гоняются на sqlite (см. phpunit.xml, DB_CONNECTION=sqlite) — та же
 * MySQL-специфичная команда `ALTER ... MODIFY` там просто syntax error и
 * валит миграции для ВСЕХ тестов, а не только для expressions (проверено:
 * без этой защиты падали все 262 теста). На sqlite поэтому ничего не
 * делаем — там 'type' остаётся ограничен старыми 4 значениями, но ни один
 * тест/фабрика linking_word не создаёт, так что это безвредно; реальные
 * данные (и единственное место, где вообще создаются linking_word) живут
 * в MySQL.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (DB::connection()->getDriverName() !== 'mysql') {
            return;
        }

        DB::statement("ALTER TABLE expressions MODIFY type ENUM('idiom', 'phrasal_verb', 'proverb', 'collocation', 'linking_word') NOT NULL");
    }

    public function down(): void
    {
        if (DB::connection()->getDriverName() !== 'mysql') {
            return;
        }

        DB::statement("ALTER TABLE expressions MODIFY type ENUM('idiom', 'phrasal_verb', 'proverb', 'collocation') NOT NULL");
    }
};
