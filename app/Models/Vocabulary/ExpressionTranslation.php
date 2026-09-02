<?php

namespace App\Models\Vocabulary;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExpressionTranslation extends Model
{
    use HasFactory;

    protected $fillable = [
        'expression_id',
        'language',
        'translation',
        'definition',
        'example',
    ];

    public function expression(): BelongsTo
    {
        return $this->belongsTo(Expression::class);
    }
}
