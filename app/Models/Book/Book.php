<?php

namespace App\Models\Book;

use App\Models\System\Level;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Book extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'author',
        'level_id',
        'description',
        'cover_image',
        'is_published',
    ];

    protected $casts = [
        'is_published' => 'boolean',
    ];

    public function level(): BelongsTo
    {
        return $this->belongsTo(Level::class);
    }

    public function pages(): HasMany
    {
        return $this->hasMany(BookPage::class)->orderBy('page_number');
    }

    public function reads(): HasMany
    {
        return $this->hasMany(BookRead::class);
    }

    public function readers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'book_reads')
            ->withPivot('current_page', 'completed_at')
            ->withTimestamps();
    }
}
