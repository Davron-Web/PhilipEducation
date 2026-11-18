<?php

namespace App\Models\Placement;

use App\Models\System\Level;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PlacementQuestion extends Model
{
    protected $fillable = ['level_id', 'question', 'order_number'];

    public function level(): BelongsTo
    {
        return $this->belongsTo(Level::class);
    }

    public function options(): HasMany
    {
        return $this->hasMany(PlacementOption::class)->orderBy('sort_order');
    }
}
