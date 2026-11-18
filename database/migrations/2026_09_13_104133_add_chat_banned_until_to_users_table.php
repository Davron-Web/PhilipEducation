<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Бан по общему чату — отдельно от is_active (тот отвечает за доступ ко
 * всей платформе, этот только за право писать в чат). NULL = не забанен;
 * дата в прошлом = бан истёк сам по себе, без отдельного задания/крона.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->timestamp('chat_banned_until')->nullable()->after('is_active');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('chat_banned_until');
        });
    }
};
