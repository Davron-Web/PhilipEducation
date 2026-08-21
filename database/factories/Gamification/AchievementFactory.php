<?php

namespace Database\Factories\Gamification;

use App\Models\Gamification\Achievement;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Achievement>
 */
class AchievementFactory extends Factory
{
    protected $model = Achievement::class;

    public function definition(): array
    {
        return [
            'title' => fake()->unique()->sentence(3),
            'description' => fake()->paragraph(),
            'icon' => fake()->optional(0.7)->imageUrl(64, 64, 'achievements'),
            'points' => fake()->numberBetween(10, 1000),
        ];
    }

    public function beginner(): static
    {
        return $this->state(fn () => [
            'title' => 'Новичок',
            'points' => 10,
        ]);
    }

    public function intermediate(): static
    {
        return $this->state(fn () => [
            'title' => 'Продвинутый ученик',
            'points' => 100,
        ]);
    }

    public function expert(): static
    {
        return $this->state(fn () => [
            'title' => 'Эксперт языка',
            'points' => 500,
        ]);
    }

    public function legendary(): static
    {
        return $this->state(fn () => [
            'title' => 'Легенда обучения',
            'points' => 1000,
        ]);
    }

    public function withoutIcon(): static
    {
        return $this->state(fn () => [
            'icon' => null,
        ]);
    }
}
