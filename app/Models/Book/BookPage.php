<?php

namespace App\Models\Book;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BookPage extends Model
{
    use HasFactory;

    protected $fillable = [
        'book_id',
        'page_number',
        'title',
        'content',
    ];

    protected $casts = [
        'page_number' => 'integer',
    ];

    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }
}
