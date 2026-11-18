<?php

namespace App\Models\Dialogue;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DialogueLine extends Model
{
    use HasFactory;

    protected $fillable = [
        'dialogue_id',
        'order_number',
        'speaker',
        'name',
        'text_en',
        'text_ru',
    ];

    protected $casts = [
        'order_number' => 'integer',
    ];

    public function dialogue(): BelongsTo
    {
        return $this->belongsTo(Dialogue::class);
    }
}
