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
            GrammarTopicSeeder::class,
            WordSeeder::class,
            WordTranslationSeeder::class,
            WordCategorySeeder::class,
            VocabularyBatch1Seeder::class,
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
            BookSeeder::class,
            IeltsTaskSeeder::class,
            IeltsPassageSeeder::class,
            IeltsSpeakingCardSeeder::class,
        ]);
    }
}
