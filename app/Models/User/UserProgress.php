<?php

namespace App\Models\User;

use App\Models\Content\Lesson;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserProgress extends Model
{
    use HasFactory;

    protected $table = 'user_progress';

    protected $fillable = [
        'user_id',
        'lesson_id',
        'progress_percent',
        'is_completed',
        'time_spent',
        'last_position',
        'completed_at',
    ];

    protected $casts = [
        'progress_percent' => 'integer',
        'time_spent' => 'integer',
        'last_position' => 'integer',
        'is_completed' => 'boolean',
        'completed_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function lesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class);
    }
}
