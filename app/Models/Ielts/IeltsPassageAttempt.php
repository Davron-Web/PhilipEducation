<?php

namespace App\Models\Ielts;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IeltsPassageAttempt extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'ielts_passage_id',
        'answers',
        'score',
        'total',
    ];

    protected $casts = [
        'answers' => 'array',
        'score' => 'integer',
        'total' => 'integer',
    ];

    public function passage(): BelongsTo
    {
        return $this->belongsTo(IeltsPassage::class, 'ielts_passage_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
