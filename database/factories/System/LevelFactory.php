<?php

namespace Database\Factories\System;

use App\Models\System\Level;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Level>
 */
class LevelFactory extends Factory
{
    protected $model = Level::class;

    public function definition(): array
    {
        $codes = ['A1', 'A2', 'B1', 'B2', 'C1', 'C2'];
        $names = [
            'A1' => 'Beginner',
            'A2' => 'Elementary',
            'B1' => 'Intermediate',
            'B2' => 'Upper-Intermediate',
            'C1' => 'Advanced',
            'C2' => 'Proficiency',
        ];

        $code = fake()->unique()->randomElement($codes) . '-' . fake()->unique()->numberBetween(1, 100000);
        $baseCode = explode('-', $code)[0];

        return [
            'code' => $code,
            'name' => $names[$baseCode],
            'description' => fake()->sentence(),
        ];
    }
}
