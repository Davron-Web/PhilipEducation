<?php

namespace Database\Factories\Book;

use App\Models\Book\Book;
use App\Models\Book\BookPage;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<BookPage>
 */
class BookPageFactory extends Factory
{
    protected $model = BookPage::class;

    public function definition(): array
    {
        return [
            'book_id' => Book::factory(),
            'page_number' => fake()->unique()->numberBetween(1, 1000),
            'title' => fake()->optional(0.3)->sentence(3),
            'content' => fake()->paragraphs(3, true),
        ];
    }
}
