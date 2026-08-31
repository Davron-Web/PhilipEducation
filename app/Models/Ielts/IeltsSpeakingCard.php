<?php

namespace App\Models\Ielts;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IeltsSpeakingCard extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'topic',
        'prompt',
        'cue_points',
        'prep_seconds',
        'speak_seconds',
    ];

    protected $casts = [
        'cue_points' => 'array',
        'prep_seconds' => 'integer',
        'speak_seconds' => 'integer',
    ];
}
