<?php

namespace Database\Factories\Test;

use App\Models\Test\Test;
use App\Models\Test\TestAttempt;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TestAttempt>
 */
class TestAttemptFactory extends Factory
{
    protected $model = TestAttempt::class;

    public function definition(): array
    {
        $startedAt = fake()->dateTimeBetween('-1 month', 'now');
        $finishedAt = (clone $startedAt)->modify('+' . fake()->numberBetween(60, 3600) . ' seconds');

        $score = fake()->numberBetween(0, 100);

        return [
            'user_id' => User::factory(),
            'test_id' => Test::factory(),
            'score' => $score,
            'passed' => $score >= 60,
            'duration_seconds' => fake()->numberBetween(60, 3600),
            'started_at' => $startedAt,
            'finished_at' => $finishedAt,
        ];
    }

    public function passed(): static
    {
        return $this->state(fn () => [
            'passed' => true,
            'score' => fake()->numberBetween(60, 100),
        ]);
    }

    public function failed(): static
    {
        return $this->state(fn () => [
            'passed' => false,
            'score' => fake()->numberBetween(0, 59),
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
