<?php

namespace App\Models\Ielts;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class IeltsPassage extends Model
{
    use HasFactory;

    protected $fillable = [
        'skill',
        'title',
        'level',
        'passage_text',
    ];

    public function questions(): HasMany
    {
        return $this->hasMany(IeltsPassageQuestion::class)->orderBy('order_number');
    }

    public function attempts(): HasMany
    {
        return $this->hasMany(IeltsPassageAttempt::class);
    }

    public function attemptsFor(User $user): HasMany
    {
        return $this->attempts()->where('user_id', $user->id)->latest();
    }
}
