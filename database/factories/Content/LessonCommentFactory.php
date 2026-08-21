<?php

namespace Database\Factories\Content;

use App\Models\Content\Lesson;
use App\Models\Content\LessonComment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<LessonComment>
 */
class LessonCommentFactory extends Factory
{
    protected $model = LessonComment::class;

    public function definition(): array
    {
        return [
            'lesson_id' => Lesson::factory(),
            'user_id' => User::factory(),
            'comment' => fake()->sentences(2, true),
        ];
    }

    public function forLesson(int $lessonId): static
    {
        return $this->state(fn () => [
            'lesson_id' => $lessonId,
        ]);
    }

    public function forUser(int $userId): static
    {
        return $this->state(fn () => [
            'user_id' => $userId,
        ]);
    }

    public function short(): static
    {
        return $this->state(fn () => [
            'comment' => fake()->sentence(),
        ]);
    }

    public function long(): static
    {
        return $this->state(fn () => [
            'comment' => fake()->paragraph(),
        ]);
    }
}
