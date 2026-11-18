<?php

namespace App\Models\Placement;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlacementAnswer extends Model
{
    protected $fillable = ['placement_attempt_id', 'placement_question_id', 'placement_option_id', 'is_correct'];

    protected $casts = ['is_correct' => 'boolean'];

    public function question(): BelongsTo
    {
        return $this->belongsTo(PlacementQuestion::class, 'placement_question_id');
    }

    public function option(): BelongsTo
    {
        return $this->belongsTo(PlacementOption::class, 'placement_option_id');
    }
}
