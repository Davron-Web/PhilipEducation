<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('plan_id')->constrained();
            $table->foreignId('subscription_id')->nullable()->constrained()->nullOnDelete();

            $table->unsignedBigInteger('amount_minor');
            $table->string('currency', 3)->default('TJS');

            // pending | paid | failed | cancelled
            $table->string('status')->default('pending');
            $table->string('provider');                       // sandbox | alif | ...
            $table->string('provider_payment_id')->nullable();
            // Наш идентификатор заказа, который уходит провайдеру.
            $table->string('reference')->unique();
            $table->timestamp('paid_at')->nullable();
            // Сырой ответ/вебхук провайдера — нужен при разборе спорных платежей.
            $table->json('payload')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index('provider_payment_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
