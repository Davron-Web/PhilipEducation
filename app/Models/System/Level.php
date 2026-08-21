<?php

namespace App\Models\System;

use App\Models\Content\Lesson;
use App\Models\Content\GrammarTopic;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Level extends Model
{
    use HasFactory;

    /**
     * Имя таблицы (на случай, если авто-поиск не сработает).
     */
    protected $table = 'levels';

    /**
     * Поля, доступные для массового заполнения.
     */
    protected $fillable = [
        'code',
        'name',
        'description',
    ];

    /**
     * Приведение типов.
     */
    protected $casts = [
        'description' => 'string',
    ];

    /*
    |--------------------------------------------------------------------------
    | Связи
    |--------------------------------------------------------------------------
    */

    /**
     * Уроки этого уровня.
     */
    public function lessons(): HasMany
    {
        return $this->hasMany(Lesson::class, 'level_id');
    }

    /**
     * Темы грамматики этого уровня.
     */
    public function grammarTopics(): HasMany
    {
        return $this->hasMany(GrammarTopic::class, 'level_id');
    }

    /**
     * Пользователи с этим уровнем.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'level_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Скоупы (по желанию)
    |--------------------------------------------------------------------------
    */

    /**
     * Сортировка по названию.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('name');
    }
}
