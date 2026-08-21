<?php

namespace Database\Factories\System;

use App\Models\System\Favorite;
use App\Models\User;
use App\Models\Vocabulary\Word;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Favorite>
 */
class FavoriteFactory extends Factory
{
    protected $model = Favorite::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'word_id' => Word::factory(),
        ];
    }

    /**
     * Создать избранное для конкретного пользователя
     */
    public function forUser(int $userId): static
    {
        return $this->state(fn () => [
            'user_id' => $userId,
        ]);
    }

    /**
     * Создать избранное для конкретного слова
     */
    public function forWord(int $wordId): static
    {
        return $this->state(fn () => [
            'word_id' => $wordId,
        ]);
    }
}
