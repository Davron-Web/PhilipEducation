<?php

namespace App\Models\Gamification;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Achievement extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'icon',
        'points',
    ];

    protected $casts = [
        'points' => 'integer',
    ];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(\App\Models\User::class, 'user_achievements')
            ->withTimestamps();
    }
}
