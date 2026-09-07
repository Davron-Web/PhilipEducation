<?php

namespace Database\Seeders;

use App\Models\Test\TestQuestion;
use App\Services\QuestionTopicResolver;
use Illuminate\Database\Seeder;

/**
 * Проставляет тему вопросам, у которых её ещё нет.
 *
 * Идемпотентен: заполняет только пустые темы, поэтому вручную заданную
 * администратором тему повторный прогон не затрёт.
 */
class QuestionTopicSeeder extends Seeder
{
    public function run(): void
    {
        $resolver = app(QuestionTopicResolver::class);
        $filled = 0;
        $skipped = 0;

        TestQuestion::with('test.lesson.grammarTopic')
            ->whereNull('topic')
            ->chunkById(200, function ($questions) use ($resolver, &$filled, &$skipped) {
                foreach ($questions as $question) {
                    $topic = $resolver->resolve($question);

                    if (! $topic) {
                        $skipped++;

                        continue;
                    }

                    $question->forceFill(['topic' => $topic])->save();
                    $filled++;
                }
            });

        $this->command?->info("Тем проставлено: {$filled}, не удалось определить: {$skipped}");
    }
}
