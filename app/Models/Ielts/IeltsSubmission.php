<?php

namespace App\Models\Ielts;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IeltsSubmission extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'ielts_task_id',
        'answer_text',
        'word_count',
        'band_score',
        'feedback',
    ];

    protected $casts = [
        'word_count' => 'integer',
        'band_score' => 'float',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function task(): BelongsTo
    {
        return $this->belongsTo(IeltsTask::class, 'ielts_task_id');
    }
}
