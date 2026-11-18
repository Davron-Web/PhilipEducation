<?php

namespace App\Models\Placement;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlacementOption extends Model
{
    protected $fillable = ['placement_question_id', 'text', 'is_correct', 'sort_order'];

    protected $casts = ['is_correct' => 'boolean'];

    public function question(): BelongsTo
    {
        return $this->belongsTo(PlacementQuestion::class, 'placement_question_id');
    }
}
