<?php

namespace App\Models\Billing;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    public const STATUS_PENDING = 'pending';
    public const STATUS_PAID = 'paid';
    public const STATUS_FAILED = 'failed';
    public const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'user_id', 'plan_id', 'subscription_id', 'amount_minor', 'currency',
        'status', 'provider', 'provider_payment_id', 'reference', 'paid_at', 'payload',
    ];

    protected $casts = [
        'paid_at' => 'datetime',
        'payload' => 'array',
        'amount_minor' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class);
    }

    public function getAmountAttribute(): float
    {
        return $this->amount_minor / 100;
    }
}
