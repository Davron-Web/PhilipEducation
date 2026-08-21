<?php

namespace App\Models\Test;

use App\Models\User\UserAnswer;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TestQuestion extends Model
{
    use HasFactory;

    protected $fillable = [
        'test_id',
        'question',
        'type',
        'points',
    ];

    protected $casts = [
        'points' => 'integer',
    ];

    public const TYPES = [
        'single_choice' => 'Один вариант',
        'multiple_choice' => 'Несколько вариантов',
        'text' => 'Текстовый ответ',
    ];

    public function getTypeLabelAttribute(): string
    {
        return self::TYPES[$this->type] ?? $this->type;
    }

    public function getTypeBadgeColorAttribute(): string
    {
        return match ($this->type) {
            'single_choice' => 'primary',
            'multiple_choice' => 'info',
            'text' => 'secondary',
            default => 'secondary',
        };
    }

    public function test(): BelongsTo
    {
        return $this->belongsTo(Test::class);
    }

    public function answers(): HasMany
    {
        return $this->hasMany(TestAnswer::class, 'question_id');
    }

    public function userAnswers(): HasMany
    {
        return $this->hasMany(UserAnswer::class, 'question_id');
    }
}
