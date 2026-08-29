<?php

namespace App\Models\Ielts;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class IeltsTask extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'title',
        'prompt',
        'chart_type',
        'chart_data',
        'topic',
        'min_words',
    ];

    protected $casts = [
        'chart_data' => 'array',
        'min_words' => 'integer',
    ];

    public function submissions(): HasMany
    {
        return $this->hasMany(IeltsSubmission::class);
    }
}
