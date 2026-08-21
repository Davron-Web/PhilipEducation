<?php

namespace Database\Factories\System;

use App\Models\System\Notification;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Notification>
 */
class NotificationFactory extends Factory
{
    protected $model = Notification::class;

    public function definition(): array
    {
        $isRead = fake()->boolean(30);
        $readAt = $isRead ? fake()->dateTimeBetween('-1 month', 'now') : null;

        return [
            'user_id' => User::factory(),
            'title' => fake()->sentence(4),
            'message' => fake()->paragraph(),
            'is_read' => $isRead,
            'read_at' => $readAt,
        ];
    }

    public function unread(): static
    {
        return $this->state(fn () => [
            'is_read' => false,
            'read_at' => null,
        ]);
    }

    public function read(): static
    {
        return $this->state(fn () => [
            'is_read' => true,
            'read_at' => fake()->dateTimeBetween('-1 month', 'now'),
        ]);
    }

    public function forUser(int $userId): static
    {
        return $this->state(fn () => [
            'user_id' => $userId,
        ]);
    }
}
