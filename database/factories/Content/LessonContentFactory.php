<?php

namespace Database\Factories\Content;

use App\Models\Content\Lesson;
use App\Models\Content\LessonContent;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<LessonContent>
 */
class LessonContentFactory extends Factory
{
    protected $model = LessonContent::class;

    public function definition(): array
    {
        return [
            'lesson_id' => Lesson::factory(),
            'type' => fake()->randomElement([
                'text',
                'video',
                'audio',
                'image',
                'exercise',
            ]),
            'title' => fake()->sentence(3),
            'content' => fake()->optional(0.7)->paragraphs(3, true),
            'file_url' => fake()->optional(0.5)->url(),
            'order_number' => fake()->numberBetween(1, 20),
        ];
    }

    public function text(): static
    {
        return $this->state(fn () => [
            'type' => 'text',
            'content' => fake()->paragraphs(4, true),
            'file_url' => null,
        ]);
    }

    public function video(): static
    {
        return $this->state(fn () => [
            'type' => 'video',
            'file_url' => fake()->url(),
            'content' => null,
        ]);
    }

    public function audio(): static
    {
        return $this->state(fn () => [
            'type' => 'audio',
            'file_url' => fake()->url(),
            'content' => null,
        ]);
    }

    public function image(): static
    {
        return $this->state(fn () => [
            'type' => 'image',
            'file_url' => fake()->imageUrl(),
            'content' => null,
        ]);
    }

    public function exercise(): static
    {
        return $this->state(fn () => [
            'type' => 'exercise',
            'content' => null,
        ]);
    }

    public function ordered(int $order): static
    {
        return $this->state(fn () => [
            'order_number' => $order,
        ]);
    }
}
