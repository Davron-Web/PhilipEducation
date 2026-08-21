<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            LevelSeeder::class,
            UserSeeder::class,
            LessonSeeder::class,
            LessonContentSeeder::class,
            WordSeeder::class,
            WordTranslationSeeder::class,
            TestSeeder::class,
            TestQuestionSeeder::class,
            TestAnswerSeeder::class,
            AchievementSeeder::class,
            UserAchievementSeeder::class,
            UserProgressSeeder::class,
            UserWordSeeder::class,
            UserResultSeeder::class,
            StudyStatisticSeeder::class,
            NotificationSeeder::class,
            CertificateSeeder::class,
        ]);
    }
}
