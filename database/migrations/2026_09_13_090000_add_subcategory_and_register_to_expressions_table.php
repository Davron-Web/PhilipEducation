<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Обе колонки nullable и ничего не переносят из существующих данных —
 * старые выражения (идиомы, фразовые глаголы и т.д.) просто остаются с
 * subcategory/register = null, ничего не ломается.
 *
 * subcategory — функциональная подгруппа внутри категории (нужна для
 * «Связующих слов»: Начало и введение / Добавление / Противопоставление /
 * Примеры / Причина и следствие / Заключение). Храним стабильный slug
 * ('intro', 'addition', ...), а не готовую подпись — подписи локализованы
 * в lang/{ru,en,tg}/site.php (см. ExpressionController::index()).
 *
 * register — регистр речи (formal/neutral/informal), чтобы ученик видел,
 * что уместно в академическом эссе, а что только в разговоре.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('expressions', function (Blueprint $table) {
            $table->string('subcategory')->nullable()->after('category');
            $table->enum('register', ['formal', 'neutral', 'informal'])->nullable()->after('subcategory');
        });
    }

    public function down(): void
    {
        Schema::table('expressions', function (Blueprint $table) {
            $table->dropColumn(['subcategory', 'register']);
        });
    }
};
