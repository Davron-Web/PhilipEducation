<?php

namespace App\Models\Billing;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Plan extends Model
{
    protected $fillable = [
        'code', 'name', 'duration_days', 'price_minor', 'currency', 'is_active', 'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'duration_days' => 'integer',
        'price_minor' => 'integer',
    ];

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    /** Цена в сомони — для показа пользователю. */
    public function getPriceAttribute(): float
    {
        return $this->price_minor / 100;
    }

    /**
     * Сколько стоит один день по этому тарифу — чтобы показать выгоду
     * длинных планов относительно недельного.
     */
    public function getPricePerDayAttribute(): float
    {
        return $this->duration_days > 0 ? $this->price / $this->duration_days : 0.0;
    }
}
