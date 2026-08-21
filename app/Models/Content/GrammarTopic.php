<?php

namespace App\Models\Content;

use App\Models\System\Level;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GrammarTopic extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'level_id',
        'order_number',
        'theory_content',
    ];

    public function level(): BelongsTo
    {
        return $this->belongsTo(Level::class);
    }

    public function lessons(): HasMany
    {
        return $this->hasMany(Lesson::class, 'grammar_topic_id');
    }
}
