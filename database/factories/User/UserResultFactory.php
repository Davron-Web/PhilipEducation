<?php

namespace Database\Factories\User;

use App\Models\Test\Test;
use App\Models\User;
use App\Models\User\UserResult;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<UserResult>
 */
class UserResultFactory extends Factory
{
    protected $model = UserResult::class;

    public function definition(): array
    {
        $score = fake()->numberBetween(0, 100);

        return [
            'user_id' => User::factory(),
            'test_id' => Test::factory(),
            'score' => $score,
            'passed' => $score >= 60,
            'attempt_date' => fake()->dateTimeBetween('-3 months', 'now'),
        ];
    }

    public function passed(): static
    {
        return $this->state(fn () => [
            'score' => fake()->numberBetween(60, 100),
            'passed' => true,
        ]);
    }

    public function failed(): static
    {
        return $this->state(fn () => [
            'score' => fake()->numberBetween(0, 59),
            'passed' => false,
        ]);
    }

    public function forUser(int $userId): static
    {
        return $this->state(fn () => [
            'user_id' => $userId,
        ]);
    }

    public function forTest(int $testId): static
    {
        return $this->state(fn () => [
            'test_id' => $testId,
        ]);
    }
}
