<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Счёт — документ об оплате, а не запись о движении денег.
     *
     * Отдельно от payments потому, что у них разное назначение и разный срок
     * жизни: payment живёт под конкретную попытку оплаты и может закончиться
     * неудачей, а invoice выписывается один раз по факту успешной оплаты и
     * дальше не меняется — на него ссылается бухгалтерия и его показывают
     * пользователю. Суммы храним копейками (дирамами), как и в payments.
     */
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('number')->unique();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('payment_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('subscription_id')->nullable()->constrained()->nullOnDelete();
            $table->string('plan_name');
            $table->unsignedInteger('amount_minor');
            $table->string('currency', 8);
            $table->string('status', 20)->default('paid');
            $table->timestamp('issued_at');
            $table->timestamps();

            $table->index(['user_id', 'issued_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
