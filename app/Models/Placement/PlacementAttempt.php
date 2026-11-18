<?php

namespace App\Models\Placement;

use App\Models\System\Level;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PlacementAttempt extends Model
{
    protected $fillable = ['user_id', 'status', 'result_level_id', 'started_at', 'completed_at'];

    protected $casts = [
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function resultLevel(): BelongsTo
    {
        return $this->belongsTo(Level::class, 'result_level_id');
    }

    public function answers(): HasMany
    {
        return $this->hasMany(PlacementAnswer::class);
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }
}
