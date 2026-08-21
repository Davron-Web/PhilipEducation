<?php

namespace App\Models\Exercise;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserExerciseAnswer extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'exercise_id',
        'question_id',
        'answer',
        'is_correct',
        'score',
    ];

    protected $casts = [
        'is_correct' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    public function exercise(): BelongsTo
    {
        return $this->belongsTo(Exercise::class);
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(ExerciseQuestion::class);
    }
}
