<?php

namespace Database\Factories\Certificate;

use App\Models\Certificate\Certificate;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Certificate>
 */
class CertificateFactory extends Factory
{
    protected $model = Certificate::class;

    public function definition(): array
    {
        $issuedAt = fake()->dateTimeBetween('-2 years', 'now');

        return [
            'user_id' => 1,
            'level_id' => 1,
            'certificate_number' => strtoupper(Str::random(4)).'-'.fake()->unique()->numerify('######'),
            'file_path' => fake()->optional(0.8)->filePath(),
            'issued_at' => $issuedAt,
        ];
    }

    public function issuedToday(): static
    {
        return $this->state(fn () => [
            'issued_at' => now(),
        ]);
    }

    public function withoutFile(): static
    {
        return $this->state(fn () => [
            'file_path' => null,
        ]);
    }

    public function withFile(): static
    {
        return $this->state(fn () => [
            'file_path' => 'certificates/'.fake()->uuid().'.pdf',
        ]);
    }

    public function recentlyIssued(): static
    {
        return $this->state(fn () => [
            'issued_at' => fake()->dateTimeBetween('-30 days', 'now'),
        ]);
    }

    public function oldCertificate(): static
    {
        return $this->state(fn () => [
            'issued_at' => fake()->dateTimeBetween('-5 years', '-2 years'),
        ]);
    }
}
