<?php

namespace App\Models\User;

// User лежит в App\Models, а этот класс — в App\Models\User,
// поэтому импорт обязателен: иначе User::class указал бы сам на себя.
use App\Models\User;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Profile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'avatar',
        'first_name',
        'last_name',
        'phone',
        'birth_date',
        'gender',
        'bio',
        'country',
        'city',
        'timezone',
        'native_language',
        'learning_goal',
    ];

    protected $casts = [
        'birth_date' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getFullNameAttribute(): string
    {
        return trim("{$this->first_name} {$this->last_name}") ?: $this->user?->name ?: 'User';
    }

    public function getAvatarUrlAttribute(): ?string
    {
        return $this->avatar ? asset('storage/'.$this->avatar) : null;
    }

    public function getAgeAttribute(): ?int
    {
        return $this->birth_date ? now()->diffInYears($this->birth_date) : null;
    }
}
