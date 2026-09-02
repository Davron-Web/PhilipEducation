<?php

namespace App\Models\Vocabulary;

use App\Models\System\Level;
use App\Models\User;
use App\Models\User\UserExpression;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Expression extends Model
{
    use HasFactory;

    protected $fillable = [
        'text',
        'type',
        'transcription',
        'example',
        'audio_url',
        'audio_checked',
        'meaning',
        'literal_translation',
        'difficulty',
        'category',
        'level_id',
        'base_verb',
        'particle',
        'separable',
    ];

    protected $casts = [
        'difficulty' => 'integer',
        'audio_checked' => 'boolean',
        'separable' => 'boolean',
    ];

    public function level(): BelongsTo
    {
        return $this->belongsTo(Level::class);
    }

    public function translations(): HasMany
    {
        return $this->hasMany(ExpressionTranslation::class);
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_expressions')
            ->withPivot('learned', 'correct_answers', 'wrong_answers', 'last_reviewed_at')
            ->withTimestamps();
    }

    public function userExpressions(): HasMany
    {
        return $this->hasMany(UserExpression::class);
    }
}
