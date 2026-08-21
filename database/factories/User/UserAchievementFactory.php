<?php

namespace Database\Factories\User;

use App\Models\Gamification\Achievement;
use App\Models\User;
use App\Models\User\UserAchievement;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<UserAchievement>
 */
class UserAchievementFactory extends Factory
{
    protected $model = UserAchievement::class;

    public function definition(): array
    {
        $earnedAt = fake()->dateTimeBetween('-1 year', 'now');

        return [
            'user_id' => User::factory(),
            'achievement_id' => Achievement::factory(),
            'earned_at' => $earnedAt,
        ];
    }

    public function forUser(int $userId): static
    {
        return $this->state(fn () => [
            'user_id' => $userId,
        ]);
    }

    public function forAchievement(int $achievementId): static
    {
        return $this->state(fn () => [
            'achievement_id' => $achievementId,
        ]);
    }

    public function recent(): static
    {
        return $this->state(fn () => [
            'earned_at' => fake()->dateTimeBetween('-7 days', 'now'),
        ]);
    }

    public function old(): static
    {
        return $this->state(fn () => [
            'earned_at' => fake()->dateTimeBetween('-2 years', '-6 months'),
        ]);
    }
}
