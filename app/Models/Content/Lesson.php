<?php

namespace App\Models\Content;

use App\Models\Exercise\Exercise;
use App\Models\System\Level;
use App\Models\Test\Test;
use App\Models\User\UserProgress;
use App\Models\Vocabulary\Word;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Lesson extends Model
{
    use HasFactory;

    protected $fillable = [
        'level_id',
        'grammar_topic_id',
        'title',
        'description',
        'order_number',
        'estimated_minutes',
        'is_published',
    ];

    protected $casts = [
        'is_published' => 'boolean',
    ];

    public function level(): BelongsTo
    {
        return $this->belongsTo(Level::class);
    }

    public function grammarTopic(): BelongsTo
    {
        return $this->belongsTo(GrammarTopic::class);
    }

    /** Видеоуроки по теме — показываются внизу страницы урока. */
    public function videos(): HasMany
    {
        return $this->hasMany(LessonVideo::class)
            ->orderBy('sort_order')
            ->orderBy('id');
    }

    public function publishedVideos(): HasMany
    {
        return $this->videos()->where('is_published', true);
    }

    public function contents(): HasMany
    {
        return $this->hasMany(LessonContent::class);
    }

    public function words(): HasMany
    {
        return $this->hasMany(Word::class);
    }

    public function tests(): HasMany
    {
        return $this->hasMany(Test::class);
    }

    public function userProgress(): HasMany
    {
        return $this->hasMany(UserProgress::class);
    }

    public function exercises(): HasMany
    {
        return $this->hasMany(Exercise::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(LessonComment::class);
    }
}
