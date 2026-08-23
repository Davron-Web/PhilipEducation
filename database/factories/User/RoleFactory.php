<?php

namespace Database\Factories\User;

use App\Models\User\Role;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Role>
 */
class RoleFactory extends Factory
{
    protected $model = Role::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->randomElement(['student', 'admin']).'-'.fake()->unique()->numberBetween(1, 100000),
            'description' => fake()->sentence(),
        ];
    }

    public function student(): static
    {
        return $this->state(fn () => [
            'name' => 'student',
            'description' => 'Ученик платформы',
        ]);
    }

    public function admin(): static
    {
        return $this->state(fn () => [
            'name' => 'admin',
            'description' => 'Администратор платформы',
        ]);
    }
}
