<?php

namespace App\Models\Exercise;

use App\Models\Content\Lesson;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Exercise extends Model
{
    use HasFactory;

    public const TYPES = [
        'fill_blank' => 'Заполнить пропуск',
        'matching' => 'Сопоставление',
        'listening' => 'Аудирование',
        'speaking' => 'Говорение',
        'translation' => 'Перевод',
    ];

    protected $fillable = [
        'lesson_id',
        'title',
        'type',
        'description',
        'instructions',
        'order_number',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function getTypeLabelAttribute(): string
    {
        return self::TYPES[$this->type] ?? $this->type;
    }

    public function getTypeBadgeColorAttribute(): string
    {
        return match ($this->type) {
            'fill_blank' => 'info',
            'matching' => 'warning',
            'listening' => 'primary',
            'speaking' => 'success',
            'translation' => 'secondary',
            default => 'secondary',
        };
    }

    public function lesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class);
    }

    public function questions(): HasMany
    {
        return $this->hasMany(ExerciseQuestion::class);
    }

    public function userAnswers(): HasMany
    {
        return $this->hasMany(UserExerciseAnswer::class);
    }
}
