<?php

namespace App\Models\Gamification;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class XpEvent extends Model
{
    protected $fillable = ['user_id', 'source', 'source_id', 'amount'];

    protected $casts = ['amount' => 'integer'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** Человеческое название источника — для ленты начислений. */
    public function label(): string
    {
        return match ($this->source) {
            'lesson' => 'Урок пройден',
            'test' => 'Тест сдан',
            'word' => 'Слово выучено',
            'achievement' => 'Достижение получено',
            default => 'Начисление',
        };
    }
}
