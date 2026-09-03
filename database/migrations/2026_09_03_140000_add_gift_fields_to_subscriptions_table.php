<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Подписку теперь можно не только купить, но и получить в подарок от
 * админа. Отделяем одно от другого, чтобы в отчётах по деньгам не
 * смешивались оплаченные подписки и выданные вручную.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            // paid — оплачена, gift — выдана администратором.
            $table->string('source')->default('paid')->after('status');
            $table->foreignId('granted_by')->nullable()->after('source')
                ->constrained('users')->nullOnDelete();
            $table->string('note')->nullable()->after('granted_by');

            // plan_id больше не обязателен: подарок может быть бессрочным
            // и не привязанным ни к какому тарифу.
            $table->foreignId('plan_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropConstrainedForeignId('granted_by');
            $table->dropColumn(['source', 'note']);
        });
    }
};
