<?php

namespace Database\Factories\User;

use App\Models\User;
use App\Models\User\UserWord;
use App\Models\Vocabulary\Word;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<UserWord>
 */
class UserWordFactory extends Factory
{
    protected $model = UserWord::class;

    public function definition(): array
    {
        $learned = fake()->boolean(35);

        return [
            'user_id' => User::factory(),
            'word_id' => Word::factory(),
            'learned' => $learned,
            'correct_answers' => fake()->numberBetween(0, $learned ? 20 : 10),
            'wrong_answers' => fake()->numberBetween(0, 10),
            'last_reviewed_at' => fake()->optional(0.7)->dateTimeBetween('-2 months', 'now'),
        ];
    }

    public function learned(): static
    {
        return $this->state(fn () => [
            'learned' => true,
            'correct_answers' => fake()->numberBetween(5, 30),
            'wrong_answers' => fake()->numberBetween(0, 5),
        ]);
    }

    public function notLearned(): static
    {
        return $this->state(fn () => [
            'learned' => false,
        ]);
    }

    public function reviewed(): static
    {
        return $this->state(fn () => [
            'last_reviewed_at' => fake()->dateTimeBetween('-1 week', 'now'),
        ]);
    }

    public function forUser(int $userId): static
    {
        return $this->state(fn () => [
            'user_id' => $userId,
        ]);
    }

    public function forWord(int $wordId): static
    {
        return $this->state(fn () => [
            'word_id' => $wordId,
        ]);
    }
}
