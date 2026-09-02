<?php

namespace App\Models\User;

use App\Models\User;
use App\Models\Vocabulary\Expression;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class UserExpression extends Pivot
{
    protected $table = 'user_expressions';

    protected $fillable = [
        'user_id',
        'expression_id',
        'learned',
        'correct_answers',
        'wrong_answers',
        'last_reviewed_at',
    ];

    protected $casts = [
        'learned' => 'boolean',
        'correct_answers' => 'integer',
        'wrong_answers' => 'integer',
        'last_reviewed_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function expression(): BelongsTo
    {
        return $this->belongsTo(Expression::class);
    }
}
