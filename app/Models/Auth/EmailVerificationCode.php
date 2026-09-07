<?php

namespace App\Models\Auth;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmailVerificationCode extends Model
{
    protected $fillable = ['user_id', 'code_hash', 'attempts', 'expires_at'];

    protected $casts = [
        'attempts' => 'integer',
        'expires_at' => 'datetime',
    ];

    /**
     * Сам код наружу не отдаём никогда: в модели его нет, только хеш.
     */
    protected $hidden = ['code_hash'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    public function minutesLeft(): int
    {
        return max(0, (int) ceil(now()->diffInSeconds($this->expires_at, false) / 60));
    }
}
