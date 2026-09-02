<?php

namespace Database\Seeders;

use App\Models\Gamification\Achievement;
use Illuminate\Database\Seeder;

/**
 * Adds machine-evaluable condition fields to achievements so
 * AchievementService::checkAndAward() can auto-grant them, and seeds the
 * new expression-based achievements. Safe to re-run: uses updateOrCreate
 * keyed on the unique 'code' column.
 */
class AchievementBatch2Seeder extends Seeder
{
    public function run(): void
    {
        // The live achievements table has no "Words Master" row to retrofit
        // (the descriptive-only seed in AchievementSeeder was never actually
        // applied as-is to this database), so add a working one from scratch.
        Achievement::updateOrCreate(
            ['code' => 'words_master_100'],
            [
                'title' => 'Мастер слов',
                'description' => 'Выучите 100 слов в разделе «Словарь».',
                'points' => 20,
                'condition_type' => 'words_learned',
                'condition_value' => 100,
            ]
        );

        Achievement::updateOrCreate(
            ['code' => 'expressions_first_10'],
            [
                'title' => 'Первые выражения',
                'description' => 'Выучите 10 идиом, фразовых глаголов, пословиц или коллокаций.',
                'points' => 10,
                'condition_type' => 'expressions_learned',
                'condition_value' => 10,
            ]
        );

        Achievement::updateOrCreate(
            ['code' => 'expressions_master_50'],
            [
                'title' => 'Мастер выражений',
                'description' => 'Выучите 50 идиом, фразовых глаголов, пословиц или коллокаций.',
                'points' => 30,
                'condition_type' => 'expressions_learned',
                'condition_value' => 50,
            ]
        );
    }
}
