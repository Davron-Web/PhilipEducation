<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            PlanSeeder::class,
            LevelSeeder::class,
            UserSeeder::class,
            LessonSeeder::class,
            LessonContentSeeder::class,
            GrammarTopicSeeder::class,
            WordSeeder::class,
            WordTranslationSeeder::class,
            WordCategorySeeder::class,
            VocabularyBatch1Seeder::class,
            VocabularyBatch2Seeder::class,
            VocabularyBatch3Seeder::class,
            VocabularyBatch4Seeder::class,
            VocabularyBatch5Seeder::class,
            ExpressionSeeder::class,
            TestSeeder::class,
            TestQuestionSeeder::class,
            TestAnswerSeeder::class,
            AchievementSeeder::class,
            AchievementBatch2Seeder::class,
            UserAchievementSeeder::class,
            UserProgressSeeder::class,
            UserWordSeeder::class,
            UserResultSeeder::class,
            StudyStatisticSeeder::class,
            NotificationSeeder::class,
            CertificateSeeder::class,
            BookSeeder::class,
            BookBatch2Seeder::class,
            IeltsTaskSeeder::class,
            IeltsPassageSeeder::class,
            IeltsListeningBatch2Seeder::class,
            IeltsSpeakingCardSeeder::class,
        ]);
    }
}
