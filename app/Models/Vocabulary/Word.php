<?php

namespace App\Models\Vocabulary;

use App\Models\Content\Lesson;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Word extends Model
{
    use HasFactory;

    protected $fillable = [
        'lesson_id',
        'word',
        'transcription',
        'example',
        'image',
        'difficulty',
        'audio_url',
        'audio_checked',
    ];

    protected $casts = [
        'difficulty' => 'integer',
        'audio_checked' => 'boolean',
    ];

    public function lesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class);
    }

    public function translations(): HasMany
    {
        return $this->hasMany(WordTranslation::class);
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_words')
            ->withPivot('learned', 'correct_answers', 'wrong_answers', 'last_reviewed_at')
            ->withTimestamps();
    }

    public function userWords(): HasMany
    {
        return $this->hasMany(\App\Models\User\UserWord::class);
    }
}
