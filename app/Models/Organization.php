<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Организация — школа, курсы или компания, ученики которой учатся вместе.
 *
 * Пока сущность не используется ни одним экраном: она существует, чтобы
 * привязка появилась в структуре данных заранее. Когда раздел понадобится,
 * добавляются роли внутри организации и глобальный scope на выборки —
 * переписывать модели и миграции не придётся.
 */
class Organization extends Model
{
    protected $fillable = ['name', 'slug', 'owner_id', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function members(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
