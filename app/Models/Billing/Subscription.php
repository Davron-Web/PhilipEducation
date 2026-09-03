<?php

namespace App\Models\Billing;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Subscription extends Model
{
    public const STATUS_PENDING = 'pending';
    public const STATUS_ACTIVE = 'active';
    public const STATUS_EXPIRED = 'expired';
    public const STATUS_CANCELLED = 'cancelled';

    public const SOURCE_PAID = 'paid';

    public const SOURCE_GIFT = 'gift';

    protected $fillable = [
        'user_id', 'plan_id', 'status', 'starts_at', 'ends_at', 'cancelled_at',
        'source', 'granted_by', 'note',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    /**
     * Подписка действует, если она активна и срок ещё не истёк.
     * Отменённая подписка продолжает действовать до конца оплаченного
     * периода — деньги за него уже получены.
     *
     * ends_at = null означает бессрочный доступ (подарок админа).
     */
    public function isActive(): bool
    {
        return in_array($this->status, [self::STATUS_ACTIVE, self::STATUS_CANCELLED], true)
            && ($this->ends_at === null || $this->ends_at->isFuture());
    }

    public function isLifetime(): bool
    {
        return $this->ends_at === null;
    }

    public function isGift(): bool
    {
        return $this->source === self::SOURCE_GIFT;
    }

    public function daysLeft(): int
    {
        return $this->ends_at && $this->ends_at->isFuture()
            ? (int) ceil(now()->diffInDays($this->ends_at, false))
            : 0;
    }
}
