<?php

namespace App\Models\LevelCheck;

use App\Models\System\Level;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LevelCheck extends Model
{
    protected $fillable = [
        'user_id',
        'input_text',
        'predicted_level_id',
        'range_low_level_id',
        'range_high_level_id',
        'probabilities',
        'sentence_count',
        'per_sentence',
    ];

    protected $casts = [
        'probabilities' => 'array',
        'per_sentence' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function predictedLevel(): BelongsTo
    {
        return $this->belongsTo(Level::class, 'predicted_level_id');
    }

    public function rangeLowLevel(): BelongsTo
    {
        return $this->belongsTo(Level::class, 'range_low_level_id');
    }

    public function rangeHighLevel(): BelongsTo
    {
        return $this->belongsTo(Level::class, 'range_high_level_id');
    }

    /** У старых записей (до range_rule) обоих полей нет — диапазон не показываем. */
    public function hasRange(): bool
    {
        return $this->range_low_level_id !== null && $this->range_high_level_id !== null;
    }
}
