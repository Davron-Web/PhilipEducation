<?php

namespace App\Models\Gamification;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudyStatistic extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'total_lessons_completed',
        'total_tests_passed',
        'total_words_learned',
        'study_time_minutes',
    ];

    protected $casts = [
        'total_lessons_completed' => 'integer',
        'total_tests_passed' => 'integer',
        'total_words_learned' => 'integer',
        'study_time_minutes' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
