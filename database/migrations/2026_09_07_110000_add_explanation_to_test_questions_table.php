<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Объяснение, почему правильный ответ именно такой.
     *
     * Показывается только после неверного ответа: увидев его до ответа,
     * ученик прочитает подсказку вместо того, чтобы думать.
     */
    public function up(): void
    {
        Schema::table('test_questions', function (Blueprint $table) {
            $table->text('explanation')->nullable()->after('topic');
        });
    }

    public function down(): void
    {
        Schema::table('test_questions', function (Blueprint $table) {
            $table->dropColumn('explanation');
        });
    }
};
