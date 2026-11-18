<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('dialogues', function (Blueprint $table) {
            // Заполняется DialoguePlaybackSeeder после того, как все реплики
            // диалога опубликованы в общий чат — не даёт повторному запуску
            // сидера продублировать те же сообщения.
            $table->timestamp('played_at')->nullable()->after('title_ru');
        });
    }

    public function down(): void
    {
        Schema::table('dialogues', function (Blueprint $table) {
            $table->dropColumn('played_at');
        });
    }
};
