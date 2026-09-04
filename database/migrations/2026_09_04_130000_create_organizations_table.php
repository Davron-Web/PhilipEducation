<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Задел под организации (школы, курсы, компании).
     *
     * Полноценной multi-tenancy здесь нет и не требуется — нужна структура,
     * которая позволит её добавить, не переписывая проект. Ключевое решение:
     * users.organization_id допускает NULL, и NULL означает «частное лицо».
     * Поэтому все нынешние 100+ учеников остаются рабочими, а разделение по
     * организациям включается позже глобальным scope, без миграции данных.
     */
    public function up(): void
    {
        Schema::create('organizations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->foreignId('owner_id')->nullable()->constrained('users')->nullOnDelete();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('organization_id')->nullable()->after('role_id')
                ->constrained()->nullOnDelete();

            // Список учеников организации — первый запрос, который появится,
            // когда раздел заработает.
            $table->index('organization_id');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['organization_id']);
            $table->dropColumn('organization_id');
        });

        Schema::dropIfExists('organizations');
    }
};
