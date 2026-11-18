<?php

namespace App\Models\Dialogue;

use App\Models\System\Level;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Dialogue extends Model
{
    use HasFactory;

    protected $fillable = [
        'slug',
        'category',
        'level_id',
        'title_en',
        'title_ru',
        'played_at',
    ];

    protected $casts = [
        'played_at' => 'datetime',
    ];

    public function level(): BelongsTo
    {
        return $this->belongsTo(Level::class);
    }

    public function lines(): HasMany
    {
        return $this->hasMany(DialogueLine::class)->orderBy('order_number');
    }
}
