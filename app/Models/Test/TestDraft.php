<?php

namespace App\Models\Test;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TestDraft extends Model
{
    protected $fillable = ['user_id', 'test_id', 'answers', 'seconds_spent'];

    protected $casts = [
        'answers' => 'array',
        'seconds_spent' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function test(): BelongsTo
    {
        return $this->belongsTo(Test::class);
    }

    /** Сколько вопросов уже отмечено — для надписи «продолжить». */
    public function answeredCount(): int
    {
        return collect($this->answers)->filter(fn ($value) => $value !== null && $value !== '')->count();
    }
}
