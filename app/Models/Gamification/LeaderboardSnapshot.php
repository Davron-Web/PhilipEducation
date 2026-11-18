<?php

namespace App\Models\Gamification;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeaderboardSnapshot extends Model
{
    protected $fillable = ['user_id', 'period', 'rank', 'points', 'snapshot_date'];

    protected $casts = [
        'rank' => 'integer',
        'points' => 'integer',
        'snapshot_date' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
