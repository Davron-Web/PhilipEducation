<?php

namespace App\Models\Exercise;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ExerciseQuestion extends Model
{
    use HasFactory;

    protected $fillable = [
        'exercise_id',
        'question',
        'correct_answer',
    ];

    public function exercise(): BelongsTo
    {
        return $this->belongsTo(Exercise::class);
    }

    public function userAnswers(): HasMany
    {
        return $this->hasMany(UserExerciseAnswer::class, 'question_id');
    }
}
