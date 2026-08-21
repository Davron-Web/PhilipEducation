<?php

namespace App\Models\Test;

use App\Models\Content\Lesson;
use App\Models\User\UserResult;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Test extends Model
{
    use HasFactory;

    protected $fillable = [
        'lesson_id',
        'title',
        'passing_score',
        'time_limit',
        'is_published',
    ];

    protected $casts = [
        'passing_score' => 'integer',
        'time_limit' => 'integer',
        'is_published' => 'boolean',
    ];

    public function lesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class);
    }

    public function questions(): HasMany
    {
        return $this->hasMany(TestQuestion::class);
    }

    public function attempts(): HasMany
    {
        return $this->hasMany(TestAttempt::class);
    }

    public function results(): HasMany
    {
        return $this->hasMany(UserResult::class);
    }
}
