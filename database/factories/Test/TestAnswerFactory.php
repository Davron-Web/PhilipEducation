<?php

namespace Database\Factories\Test;

use App\Models\Test\TestAnswer;
use App\Models\Test\TestQuestion;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TestAnswer>
 */
class TestAnswerFactory extends Factory
{
    protected $model = TestAnswer::class;

    public function definition(): array
    {
        return [
            'question_id' => TestQuestion::factory(),
            'answer' => fake()->sentence(3),
            'is_correct' => fake()->boolean(20),
        ];
    }

    public function correct(): static
    {
        return $this->state(fn () => [
            'is_correct' => true,
        ]);
    }

    public function incorrect(): static
    {
        return $this->state(fn () => [
            'is_correct' => false,
        ]);
    }

    public function forQuestion(int $questionId): static
    {
        return $this->state(fn () => [
            'question_id' => $questionId,
        ]);
    }
}
