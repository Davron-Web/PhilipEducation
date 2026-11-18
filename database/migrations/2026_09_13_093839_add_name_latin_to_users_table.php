<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Имя латиницей — отдельно от основного name (может быть на кириллице
 * или таджикском), нужно для сертификата: DomPDF со стандартным шрифтом
 * не отрисует кириллицу без явного шрифта, а плашка на PDF-документе
 * обычно печатается латиницей независимо от языка интерфейса. Nullable —
 * заполняется в профиле по желанию, если пусто, сертификат показывает
 * обычное name (см. CertificateService/AchievementController).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('name_latin')->nullable()->after('name');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('name_latin');
        });
    }
};
