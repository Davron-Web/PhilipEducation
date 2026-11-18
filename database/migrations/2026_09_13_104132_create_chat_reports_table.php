<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Жалобы на сообщения чата — видны только админу (см. Admin\Chat\ReportController).
 * unique(chat_message_id, reported_by) — один и тот же человек не может
 * пожаловаться на одно и то же сообщение дважды (повторный клик по кнопке
 * не плодит дубликаты и не должен выглядеть как ошибка).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chat_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chat_message_id')->constrained()->cascadeOnDelete();
            $table->foreignId('reported_by')->constrained('users')->cascadeOnDelete();
            $table->text('reason')->nullable();
            $table->timestamps();

            $table->unique(['chat_message_id', 'reported_by']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chat_reports');
    }
};
