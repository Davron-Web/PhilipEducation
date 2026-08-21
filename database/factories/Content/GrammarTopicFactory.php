<?php

namespace Database\Factories\Content;

use App\Models\Content\GrammarTopic;
use App\Models\System\Level;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<GrammarTopic>
 */
class GrammarTopicFactory extends Factory
{
    protected $model = GrammarTopic::class;

    public function definition(): array
    {
        return [
            'level_id' => Level::factory(),
            'title' => fake()->sentence(3),
            'theory' => fake()->paragraphs(5, true),
        ];
    }

    public function basic(): static
    {
        return $this->state(fn () => [
            'title' => 'Basic Grammar',
            'theory' => 'Basic rules of English grammar including tenses and sentence structure.',
        ]);
    }

    public function advanced(): static
    {
        return $this->state(fn () => [
            'title' => 'Advanced Grammar',
            'theory' => fake()->paragraphs(8, true),
        ]);
    }

    public function forLevel(int $levelId): static
    {
        return $this->state(fn () => [
            'level_id' => $levelId,
        ]);
    }
}
