<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Коды подтверждения почты.
     *
     * Храним хеш, а не сам код: у того, кто получит доступ к базе, не должно
     * оказаться готового ключа к чужим аккаунтам. Хеш быстрый (sha256), а не
     * bcrypt — код живёт 15 минут, попыток даётся пять, и медленный хеш здесь
     * защищает не от перебора, а только от нас самих на каждом вводе.
     *
     * По одной строке на пользователя: новый код заменяет прежний, поэтому
     * старый перестаёт работать сразу, а не живёт параллельно.
     */
    public function up(): void
    {
        Schema::create('email_verification_codes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('code_hash', 64);
            $table->unsignedTinyInteger('attempts')->default(0);
            $table->timestamp('expires_at');
            $table->timestamps();

            // Для уборки просроченных кодов по расписанию.
            $table->index('expires_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('email_verification_codes');
    }
};
