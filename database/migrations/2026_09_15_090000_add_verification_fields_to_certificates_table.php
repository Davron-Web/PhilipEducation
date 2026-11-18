<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Достраивает существующую таблицу certificates (сейчас — ручная выдача
 * из админки: пользователь, уровень, номер, необязательный файл) под
 * автоматическую выдачу и публичную проверку. Не создаём вторую таблицу —
 * на момент миграции в certificates 0 строк, и один и тот же уровень не
 * должен быть выдан пользователю дважды независимо от того, кто его
 * выдал, админ вручную или сервис автоматически.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('certificates', function (Blueprint $table) {
            $table->uuid('certificate_uuid')->nullable()->unique()->after('id');
            $table->json('metadata')->nullable()->after('issued_at');

            $table->unique(['user_id', 'level_id']);
        });
    }

    public function down(): void
    {
        Schema::table('certificates', function (Blueprint $table) {
            $table->dropUnique(['user_id', 'level_id']);
            $table->dropColumn(['certificate_uuid', 'metadata']);
        });
    }
};
