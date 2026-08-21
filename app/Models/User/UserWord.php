<?php

namespace App\Models\User;

use Illuminate\Database\Eloquent\Relations\Pivot;

class UserWord extends Pivot
{
    protected $table = 'user_words';

    protected $fillable = [
        'user_id',
        'word_id',
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
}
