<?php

namespace Database\Seeders;

use App\Models\Billing\Plan;
use Illuminate\Database\Seeder;

/**
 * Тарифы подписки. Цены — ПРЕДВАРИТЕЛЬНЫЕ, их нужно заменить на реальные:
 * они заданы в дирамах (1 сомони = 100 дирамов), чтобы не терять копейки
 * на округлениях float.
 *
 * Логика цен: месяц выгоднее четырёх недель, год выгоднее двенадцати
 * месяцев — иначе длинные тарифы никто не берёт.
 */
class PlanSeeder extends Seeder
{
    public function run(): void
    {
        $plans = [
            ['code' => 'weekly',  'name' => 'Неделя', 'duration_days' => 7,   'price_minor' => 2500,  'sort_order' => 1],
            ['code' => 'monthly', 'name' => 'Месяц',  'duration_days' => 30,  'price_minor' => 7900,  'sort_order' => 2],
            ['code' => 'yearly',  'name' => 'Год',    'duration_days' => 365, 'price_minor' => 69000, 'sort_order' => 3],
        ];

        foreach ($plans as $plan) {
            Plan::updateOrCreate(
                ['code' => $plan['code']],
                $plan + ['currency' => 'TJS', 'is_active' => true],
            );
        }

        $this->command?->info('PlanSeeder: тарифы обновлены ('.count($plans).').');
    }
}
