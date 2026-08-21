<?php

namespace Database\Factories\Vocabulary;

use App\Models\Vocabulary\Word;
use App\Models\Vocabulary\WordTranslation;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<WordTranslation>
 */
class WordTranslationFactory extends Factory
{
    protected $model = WordTranslation::class;

    public function definition(): array
    {
        return [
            'word_id' => Word::factory(),
            'language' => fake()->randomElement([
                'en',
                'ru',
                'de',
                'fr',
                'es',
            ]),
            'translation' => fake()->word(),
        ];
    }

    public function english(): static
    {
        return $this->state(fn () => [
            'language' => 'en',
            'translation' => fake()->word(),
        ]);
    }

    public function russian(): static
    {
        return $this->state(fn () => [
            'language' => 'ru',
            'translation' => fake()->word(),
        ]);
    }

    public function forWord(int $wordId): static
    {
        return $this->state(fn () => [
            'word_id' => $wordId,
        ]);
    }

    public function commonLanguages(): static
    {
        return $this->state(fn () => [
            'language' => fake()->randomElement(['en', 'ru']),
        ]);
    }
}
