<?php

namespace Database\Factories\Vocabulary;

use App\Models\Content\Lesson;
use App\Models\Vocabulary\Word;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Word>
 */
class WordFactory extends Factory
{
    protected $model = Word::class;

    public function definition(): array
    {
        return [
            'lesson_id' => fake()->optional(0.6)->randomElement([
                null,
                Lesson::factory(),
            ]),
            'word' => fake()->unique()->word(),
            'transcription' => fake()->optional(0.7)->lexify('/?????/'),
            'audio_url' => fake()->optional(0.5)->url(),
            'image' => fake()->optional(0.5)->imageUrl(300, 300, 'words'),
        ];
    }

    public function withoutLesson(): static
    {
        return $this->state(fn () => [
            'lesson_id' => null,
        ]);
    }

    public function withMedia(): static
    {
        return $this->state(fn () => [
            'transcription' => '/' . fake()->word() . '/',
            'audio_url' => fake()->url(),
            'image' => fake()->imageUrl(300, 300, 'words'),
        ]);
    }

    public function minimal(): static
    {
        return $this->state(fn () => [
            'transcription' => null,
            'audio_url' => null,
            'image' => null,
        ]);
    }
}
