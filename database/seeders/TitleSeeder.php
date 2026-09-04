<?php

namespace Database\Seeders;

use App\Models\Gamification\Title;
use Illuminate\Database\Seeder;

/**
 * Пороги подобраны под реальные начисления (XpService::REWARDS): урок — 25,
 * тест — 40, слово — 2. «Ученик» достаётся за первый же урок, «Наставник» —
 * это уже сотни занятий, чтобы верхнее звание не обесценивалось.
 */
class TitleSeeder extends Seeder
{
    public function run(): void
    {
        $titles = [
            ['code' => 'novice', 'name' => 'Новичок', 'description' => 'Добро пожаловать — путь только начинается.', 'icon' => '🌱', 'min_xp' => 0],
            ['code' => 'student', 'name' => 'Ученик', 'description' => 'Первые уроки позади.', 'icon' => '📘', 'min_xp' => 100],
            ['code' => 'diligent', 'name' => 'Прилежный', 'description' => 'Занятия вошли в привычку.', 'icon' => '✏️', 'min_xp' => 500],
            ['code' => 'polyglot', 'name' => 'Полиглот', 'description' => 'Словарный запас растёт быстро.', 'icon' => '🗣️', 'min_xp' => 1500],
            ['code' => 'expert', 'name' => 'Знаток', 'description' => 'Уверенное владение материалом.', 'icon' => '🎓', 'min_xp' => 4000],
            ['code' => 'master', 'name' => 'Мастер', 'description' => 'Сотни занятий и десятки сданных тестов.', 'icon' => '🏆', 'min_xp' => 10000],
            ['code' => 'mentor', 'name' => 'Наставник', 'description' => 'Высшее звание платформы.', 'icon' => '👑', 'min_xp' => 25000],
        ];

        foreach ($titles as $title) {
            Title::firstOrCreate(['code' => $title['code']], $title);
        }
    }
}
