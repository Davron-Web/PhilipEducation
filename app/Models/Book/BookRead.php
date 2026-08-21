<?php

namespace App\Models\Book;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BookRead extends Model
{
    use HasFactory;

    protected $table = 'book_reads';

    protected $fillable = [
        'user_id',
        'book_id',
        'current_page',
        'completed_at',
    ];

    protected $casts = [
        'current_page' => 'integer',
        'completed_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }
}
