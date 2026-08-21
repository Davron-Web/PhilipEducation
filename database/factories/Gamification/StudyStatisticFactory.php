<?php

namespace Database\Factories\Gamification;

use App\Models\Gamification\StudyStatistic;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<StudyStatistic>
 */
class StudyStatisticFactory extends Factory
{
    protected $model = StudyStatistic::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),

            'total_lessons_completed' => fake()->numberBetween(0, 200),
            'total_tests_passed' => fake()->numberBetween(0, 100),
            'total_words_learned' => fake()->numberBetween(0, 2000),
            'study_time_minutes' => fake()->numberBetween(0, 5000),
        ];
    }

    public function empty(): static
    {
        return $this->state(fn () => [
            'total_lessons_completed' => 0,
            'total_tests_passed' => 0,
            'total_words_learned' => 0,
            'study_time_minutes' => 0,
        ]);
    }

    public function active(): static
    {
        return $this->state(fn () => [
            'total_lessons_completed' => fake()->numberBetween(50, 200),
            'total_tests_passed' => fake()->numberBetween(20, 100),
            'total_words_learned' => fake()->numberBetween(500, 2000),
            'study_time_minutes' => fake()->numberBetween(1000, 5000),
        ]);
    }

    public function beginner(): static
    {
        return $this->state(fn () => [
            'total_lessons_completed' => fake()->numberBetween(0, 10),
            'total_tests_passed' => fake()->numberBetween(0, 5),
            'total_words_learned' => fake()->numberBetween(0, 200),
            'study_time_minutes' => fake()->numberBetween(0, 300),
        ]);
    }
}
