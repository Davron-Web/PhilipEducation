<?php

namespace Database\Factories\Exercise;

use App\Models\Exercise\Exercise;
use App\Models\Exercise\ExerciseQuestion;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ExerciseQuestion>
 */
class ExerciseQuestionFactory extends Factory
{
    protected $model = ExerciseQuestion::class;

    public function definition(): array
    {
        return [
            'exercise_id' => Exercise::factory(),
            'question' => fake()->sentence(10).'?',
            'correct_answer' => fake()->word(),
        ];
    }

    public function withLongAnswer(): static
    {
        return $this->state(fn () => [
            'correct_answer' => fake()->sentence(6),
        ]);
    }

    public function simpleWordAnswer(): static
    {
        return $this->state(fn () => [
            'correct_answer' => fake()->word(),
        ]);
    }

    public function translationStyle(): static
    {
        return $this->state(fn () => [
            'question' => 'Переведите предложение',
            'correct_answer' => fake()->sentence(5),
        ]);
    }
}
