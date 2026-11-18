<?php

namespace Database\Factories;

use App\Models\System\Level;
use App\Models\User;
use App\Models\User\Role;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    protected $model = User::class;

    protected static ?string $password = null;

    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),

            'role_id' => Role::factory(),

            // level может быть null
            'level_id' => fake()->optional(0.6)->randomElement([
                null,
                Level::factory(),
            ]),

            'points' => fake()->numberBetween(0, 5000),
            'is_active' => fake()->boolean(90),

            'last_login_at' => fake()->optional(0.7)->dateTimeBetween('-2 months', 'now'),

            'remember_token' => Str::random(10),
        ];
    }

    public function unverified(): static
    {
        return $this->state(fn () => [
            'email_verified_at' => null,
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn () => [
            'is_active' => false,
            'last_login_at' => null,
        ]);
    }

    public function active(): static
    {
        return $this->state(fn () => [
            'is_active' => true,
        ]);
    }

    public function withoutLevel(): static
    {
        return $this->state(fn () => [
            'level_id' => null,
        ]);
    }

    public function highLevel(): static
    {
        return $this->state(fn () => [
            'points' => fake()->numberBetween(3000, 10000),
        ]);
    }

    public function admin(): static
    {
        return $this->state(fn () => [
            'points' => fake()->numberBetween(2000, 10000),
            'is_active' => true,
        ]);
    }

    /**
     * Фейковый пользователь для наполнения общего чата (см. ChatBotSeeder,
     * команда chat:bots). $lang — закреплённый язык бота ('tg' или 'ru');
     * не передан — выбирается случайно. Имя — из пула СВОЕГО языка
     * (config/chat_bots.php: tg-имена таджикские, ru-имена русские), не
     * fake()->name(): последний даёт случайные англо-русские имена
     * вперемешку, а тут имя должно соответствовать языку, на котором бот
     * реально пишет. avatar — сразу имя файла из готового набора
     * (User::randomAvatarFilename()), а не null: у ботов реального фото
     * никогда не будет, ждать нечего.
     */
    public function bot(?string $lang = null): static
    {
        return $this->state(function () use ($lang) {
            $lang ??= fake()->randomElement(['tg', 'ru']);
            $names = config("chat_bots.names.{$lang}");
            $isMale = fake()->boolean();

            $first = fake()->randomElement($isMale ? $names['male_first'] : $names['female_first']);
            $last = fake()->randomElement($isMale ? $names['surname_male'] : $names['surname_female']);
            $name = "{$first} {$last}";

            return [
                'name' => $name,
                // Обычная роль "student", не Role::factory(): иначе каждый
                // бот тянул бы за собой отдельную случайную роль в БД.
                'role_id' => Role::where('name', 'student')->value('id'),
                'is_bot' => true,
                'lang' => $lang,
                'is_active' => true,
                'avatar' => User::randomAvatarFilename(),
                'points' => fake()->numberBetween(0, 400),
            ];
        });
    }
}
