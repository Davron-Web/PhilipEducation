<?php

namespace App\Models\Chat;

use App\Models\User;
use Database\Factories\Chat\ChatMessageFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ChatMessage extends Model
{
    use HasFactory;

    protected static string $factory = ChatMessageFactory::class;

    protected $fillable = [
        'user_id',
        'body',
        'is_deleted',
        'deleted_by',
    ];

    protected $casts = [
        'is_deleted' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function deletedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }

    public function reports(): HasMany
    {
        return $this->hasMany(ChatReport::class);
    }

    /** Скрытые (is_deleted=true) сообщения не должны попадать в ленту чата. */
    public function scopeVisible(Builder $query): Builder
    {
        return $query->where('is_deleted', false);
    }
}
