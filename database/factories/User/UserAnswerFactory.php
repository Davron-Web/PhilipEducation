<?php

namespace Database\Factories\User;

use App\Models\Test\TestAttempt;
use App\Models\Test\TestQuestion;
use App\Models\User\UserAnswer;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<UserAnswer>
 */
class UserAnswerFactory extends Factory
{
    protected $model = UserAnswer::class;

    public function definition(): array
    {
        $isCorrect = fake()->boolean(70);

        return [
            'attempt_id' => TestAttempt::factory(),
            'question_id' => TestQuestion::factory(),
            'answer' => fake()->sentence(3),
            'is_correct' => $isCorrect,
        ];
    }

    public function correct(): static
    {
        return $this->state(fn () => [
            'is_correct' => true,
            'answer' => fake()->word(),
        ]);
    }

    public function incorrect(): static
    {
        return $this->state(fn () => [
            'is_correct' => false,
            'answer' => fake()->sentence(4),
        ]);
    }

    public function forAttempt(int $attemptId): static
    {
        return $this->state(fn () => [
            'attempt_id' => $attemptId,
        ]);
    }

    public function forQuestion(int $questionId): static
    {
        return $this->state(fn () => [
            'question_id' => $questionId,
        ]);
    }
}
