<?php

namespace Database\Factories\User;

use App\Models\Content\Lesson;
use App\Models\User;
use App\Models\User\UserProgress;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<UserProgress>
 */
class UserProgressFactory extends Factory
{
    protected $model = UserProgress::class;

    public function definition(): array
    {
        $completed = fake()->boolean(40);

        return [
            'user_id' => User::factory(),
            'lesson_id' => Lesson::factory(),
            'progress' => $completed
                ? 100
                : fake()->numberBetween(0, 99),

            'completed' => $completed,
            'completed_at' => $completed
                ? fake()->dateTimeBetween('-1 month', 'now')
                : null,
        ];
    }

    public function completed(): static
    {
        return $this->state(fn () => [
            'progress' => 100,
            'completed' => true,
            'completed_at' => fake()->dateTimeBetween('-6 months', 'now'),
        ]);
    }

    public function inProgress(): static
    {
        return $this->state(fn () => [
            'completed' => false,
            'completed_at' => null,
            'progress' => fake()->numberBetween(1, 99),
        ]);
    }

    public function forUser(int $userId): static
    {
        return $this->state(fn () => [
            'user_id' => $userId,
        ]);
    }

    public function forLesson(int $lessonId): static
    {
        return $this->state(fn () => [
            'lesson_id' => $lessonId,
        ]);
    }
}
