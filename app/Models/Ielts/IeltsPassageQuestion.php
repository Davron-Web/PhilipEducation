<?php

namespace App\Models\Ielts;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IeltsPassageQuestion extends Model
{
    use HasFactory;

    protected $fillable = [
        'ielts_passage_id',
        'question',
        'options',
        'correct_index',
        'order_number',
    ];

    protected $casts = [
        'options' => 'array',
        'correct_index' => 'integer',
        'order_number' => 'integer',
    ];

    public function passage(): BelongsTo
    {
        return $this->belongsTo(IeltsPassage::class, 'ielts_passage_id');
    }
}
