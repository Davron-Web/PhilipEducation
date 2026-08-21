<?php

namespace Database\Factories\Content;

use App\Models\Content\Lesson;
use App\Models\System\Level;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Lesson>
 */
class LessonFactory extends Factory
{
    protected $model = Lesson::class;

    public function definition(): array
    {
        return [
            'level_id' => Level::factory(),
            'title' => fake()->sentence(4),
            'description' => fake()->optional(0.7)->paragraph(),
            'order_number' => fake()->numberBetween(1, 50),
            'estimated_minutes' => fake()->numberBetween(5, 60),
            'is_published' => fake()->boolean(85),
        ];
    }

    public function published(): static
    {
        return $this->state(fn () => [
            'is_published' => true,
        ]);
    }

    public function draft(): static
    {
        return $this->state(fn () => [
            'is_published' => false,
        ]);
    }

    public function short(): static
    {
        return $this->state(fn () => [
            'estimated_minutes' => fake()->numberBetween(5, 15),
        ]);
    }

    public function long(): static
    {
        return $this->state(fn () => [
            'estimated_minutes' => fake()->numberBetween(30, 120),
        ]);
    }
}
