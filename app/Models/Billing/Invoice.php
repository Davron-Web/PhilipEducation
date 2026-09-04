<?php

namespace App\Models\Billing;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Invoice extends Model
{
    public const STATUS_PAID = 'paid';
    public const STATUS_REFUNDED = 'refunded';

    protected $fillable = [
        'number', 'user_id', 'payment_id', 'subscription_id',
        'plan_name', 'amount_minor', 'currency', 'status', 'issued_at',
    ];

    protected $casts = [
        'issued_at' => 'datetime',
        'amount_minor' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class);
    }

    public function getAmountAttribute(): float
    {
        return $this->amount_minor / 100;
    }

    /**
     * Номер вида INV-2026-000123.
     *
     * Нумерация сквозная по годам и опирается на id платежа, а не на счётчик
     * счетов: так два одновременных платежа не получат одинаковый номер без
     * блокировки таблицы.
     */
    public static function numberFor(Payment $payment): string
    {
        return sprintf('INV-%s-%06d', now()->year, $payment->id);
    }
}
