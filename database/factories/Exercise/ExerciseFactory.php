<?php

namespace Database\Factories\Exercise;

use App\Models\Content\Lesson;
use App\Models\Exercise\Exercise;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Exercise>
 */
class ExerciseFactory extends Factory
{
    protected $model = Exercise::class;

    public function definition(): array
    {
        return [
            'lesson_id' => Lesson::factory(),
            'title' => fake()->sentence(4),
            'type' => fake()->randomElement([
                'fill_blank',
                'matching',
                'listening',
                'speaking',
                'translation',
            ]),
            'instructions' => fake()->paragraphs(3, true),
        ];
    }

    public function fillBlank(): static
    {
        return $this->state(fn () => [
            'type' => 'fill_blank',
            'title' => 'Заполните пропуски',
            'instructions' => 'Вставьте подходящие слова в пропуски.',
        ]);
    }

    public function matching(): static
    {
        return $this->state(fn () => [
            'type' => 'matching',
            'title' => 'Сопоставление',
            'instructions' => 'Соедините элементы из левой и правой колонок.',
        ]);
    }

    public function listening(): static
    {
        return $this->state(fn () => [
            'type' => 'listening',
            'title' => 'Аудирование',
            'instructions' => 'Прослушайте аудио и ответьте на вопросы.',
        ]);
    }

    public function speaking(): static
    {
        return $this->state(fn () => [
            'type' => 'speaking',
            'title' => 'Устная практика',
            'instructions' => 'Произнесите фразы и запишите ответ.',
        ]);
    }

    public function translation(): static
    {
        return $this->state(fn () => [
            'type' => 'translation',
            'title' => 'Перевод',
            'instructions' => 'Переведите предложения на указанный язык.',
        ]);
    }
}
