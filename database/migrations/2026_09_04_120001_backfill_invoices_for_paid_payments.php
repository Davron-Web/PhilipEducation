<?php

use App\Models\Billing\Invoice;
use App\Models\Billing\Payment;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Выписывает счета по платежам, оплаченным до появления таблицы invoices.
     *
     * Без этого отчёт о доходе показывал бы ноль при реально прошедших
     * оплатах: доход считается по счетам, а у старых платежей их нет.
     */
    public function up(): void
    {
        Payment::where('status', Payment::STATUS_PAID)
            ->whereDoesntHave('invoice')
            ->with('plan')
            ->chunkById(100, function ($payments) {
                foreach ($payments as $payment) {
                    Invoice::firstOrCreate(
                        ['payment_id' => $payment->id],
                        [
                            'number' => Invoice::numberFor($payment),
                            'user_id' => $payment->user_id,
                            'subscription_id' => $payment->subscription_id,
                            'plan_name' => $payment->plan?->name ?? 'Подписка',
                            'amount_minor' => $payment->amount_minor,
                            'currency' => $payment->currency,
                            'status' => Invoice::STATUS_PAID,
                            'issued_at' => $payment->paid_at ?? $payment->created_at,
                        ]
                    );
                }
            });
    }

    public function down(): void
    {
        // Счета не удаляем: это финансовые документы, а не производные данные.
    }
};
