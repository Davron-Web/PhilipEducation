<?php

namespace Database\Factories\Test;

use App\Models\Test\Test;
use App\Models\Test\TestQuestion;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TestQuestion>
 */
class TestQuestionFactory extends Factory
{
    protected $model = TestQuestion::class;

    public function definition(): array
    {
        return [
            'test_id' => Test::factory(),
            'question' => fake()->sentence(10) . '?',
            'type' => fake()->randomElement([
                'single_choice',
                'multiple_choice',
                'text',
            ]),
            'points' => fake()->numberBetween(1, 5),
        ];
    }

    public function singleChoice(): static
    {
        return $this->state(fn () => [
            'type' => 'single_choice',
        ]);
    }

    public function multipleChoice(): static
    {
        return $this->state(fn () => [
            'type' => 'multiple_choice',
        ]);
    }

    public function text(): static
    {
        return $this->state(fn () => [
            'type' => 'text',
            'points' => 1,
        ]);
    }

    public function highPoints(): static
    {
        return $this->state(fn () => [
            'points' => fake()->numberBetween(3, 10),
        ]);
    }

    public function forTest(int $testId): static
    {
        return $this->state(fn () => [
            'test_id' => $testId,
        ]);
    }
}
