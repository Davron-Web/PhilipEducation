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
        Schema::table('users', function (Blueprint $table) {
            // Добавляем поле для хранения ID завершенных уроков (массив в формате JSON)
            $table->json('completed_lessons')->nullable()->after('role_id');

            // Добавляем общий счетчик выполненных заданий (по умолчанию 0)
            $table->unsignedInteger('total_tasks_completed')->default(0)->after('completed_lessons');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Удаляем эти поля при откате миграции
            $table->dropColumn(['completed_lessons', 'total_tasks_completed']);
        });
    }
};
