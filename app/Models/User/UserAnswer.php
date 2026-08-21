<?php

namespace App\Models\User;

use App\Models\Test\TestAttempt;
use App\Models\Test\TestQuestion;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserAnswer extends Model
{
    use HasFactory;

    protected $fillable = [
        'attempt_id',
        'question_id',
        'answer',
        'is_correct',
    ];

    protected $casts = [
        'is_correct' => 'boolean',
    ];

    public function attempt(): BelongsTo
    {
        return $this->belongsTo(TestAttempt::class);
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(TestQuestion::class);
    }
}
