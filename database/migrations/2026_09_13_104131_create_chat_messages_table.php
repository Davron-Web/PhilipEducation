<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Общий чат — одна комната на всю платформу, без приватных диалогов.
 * Удаление всегда мягкое (is_deleted) — нужно для разбора жалоб: если
 * строку стирать физически, модератору после жалобы будет нечего смотреть.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chat_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->text('body');
            $table->boolean('is_deleted')->default(false);
            // Кто скрыл сообщение — сам автор или админ по жалобе; nullOnDelete,
            // а не cascade, чтобы при удалении модератора история модерации
            // не пропадала вместе с ним.
            $table->foreignId('deleted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chat_messages');
    }
};
