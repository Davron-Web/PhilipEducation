<?php

namespace Database\Factories\Test;

use App\Models\Content\Lesson;
use App\Models\Test\Test;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Test>
 */
class TestFactory extends Factory
{
    protected $model = Test::class;

    public function definition(): array
    {
        return [
            'lesson_id' => Lesson::factory(),
            'title' => fake()->sentence(4),
            'passing_score' => fake()->numberBetween(50, 90),
            'time_limit' => fake()->optional(0.7)->numberBetween(300, 3600), // 5–60 min
        ];
    }

    public function easy(): static
    {
        return $this->state(fn () => [
            'passing_score' => 50,
            'time_limit' => 1800,
        ]);
    }

    public function medium(): static
    {
        return $this->state(fn () => [
            'passing_score' => 70,
            'time_limit' => 1200,
        ]);
    }

    public function hard(): static
    {
        return $this->state(fn () => [
            'passing_score' => 85,
            'time_limit' => 900,
        ]);
    }

    public function noTimeLimit(): static
    {
        return $this->state(fn () => [
            'time_limit' => null,
        ]);
    }

    public function forLesson(int $lessonId): static
    {
        return $this->state(fn () => [
            'lesson_id' => $lessonId,
        ]);
    }
}
