<?php

namespace App\Models\Vocabulary;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Builder;

class Collection extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'slug',
        'title_ru',
        'title_en',
        'title_tg',
        'description',
        'sort_order',
        'is_published',
    ];

    protected $casts = [
        'sort_order' => 'integer',
        'is_published' => 'boolean',
    ];

    /**
     * Позволяет route model binding по slug: Route::get('/{collection:slug}').
     * Slug уникален только в паре с type, поэтому если когда-нибудь появится
     * совпадение между типами, привязка возьмёт первую попавшуюся — на
     * практике сейчас коллекции резолвятся по отдельным типизированным
     * секциям хаба, где это не пересекается.
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function words(): BelongsToMany
    {
        return $this->belongsToMany(Word::class, 'collection_word')
            ->withPivot('sort_order')
            ->withTimestamps()
            ->orderByPivot('sort_order');
    }

    /**
     * Заголовок на текущей локали интерфейса с откатом на русский — у
     * перенесённых из category тем перевод на en/tg может отсутствовать
     * (см. миграцию add_en_tg_titles_to_topic_collections), и показывать
     * пустую строку хуже, чем показать русский оригинал.
     */
    public function getTitleAttribute(): string
    {
        $locale = app()->getLocale();

        return $this->{"title_{$locale}"} ?: $this->title_ru;
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }

    public function scopeOfType(Builder $query, string $type): Builder
    {
        return $query->where('type', $type);
    }
}
