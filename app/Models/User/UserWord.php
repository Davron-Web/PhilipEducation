<?php

namespace App\Models\User;

use App\Models\User;
use App\Models\Vocabulary\Word;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class UserWord extends Pivot
{
    protected $table = 'user_words';

    /**
     * Pivot по умолчанию считает, что первичного ключа нет, и тогда
     * save()/update() по существующей записи не находят строку. У таблицы
     * user_words есть автоинкрементный id, поэтому включаем его обратно —
     * без этого прогресс повторений молча не сохранялся бы.
     */
    public $incrementing = true;

    protected $fillable = [
        'user_id',
        'word_id',
        'learned',
        'correct_answers',
        'wrong_answers',
        'last_reviewed_at',
        'repetitions',
        'interval_days',
        'ease_factor',
        'next_review_at',
    ];

    protected $casts = [
        'learned' => 'boolean',
        'correct_answers' => 'integer',
        'wrong_answers' => 'integer',
        'last_reviewed_at' => 'datetime',
        'repetitions' => 'integer',
        'interval_days' => 'integer',
        'ease_factor' => 'float',
        'next_review_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function word(): BelongsTo
    {
        return $this->belongsTo(Word::class);
    }
}
