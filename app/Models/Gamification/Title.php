<?php

namespace App\Models\Gamification;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Title extends Model
{
    use HasFactory;

    protected $fillable = ['code', 'name', 'description', 'icon', 'min_xp', 'is_active'];

    protected $casts = [
        'min_xp' => 'integer',
        'is_active' => 'boolean',
    ];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_titles')
            ->withPivot('earned_at')
            ->withTimestamps();
    }
}
