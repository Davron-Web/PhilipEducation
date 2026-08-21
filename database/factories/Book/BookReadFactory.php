<?php

namespace Database\Factories\Book;

use App\Models\Book\Book;
use App\Models\Book\BookRead;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<BookRead>
 */
class BookReadFactory extends Factory
{
    protected $model = BookRead::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'book_id' => Book::factory(),
            'current_page' => fake()->numberBetween(1, 10),
            'completed_at' => null,
        ];
    }
}
