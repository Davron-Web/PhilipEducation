<?php

namespace Database\Seeders;

use App\Models\Ielts\IeltsPassage;
use App\Models\Ielts\IeltsSpeakingCard;
use App\Models\Ielts\IeltsTask;
use Illuminate\Database\Seeder;

/**
 * По 20 материалов в каждый раздел IELTS: Reading, Listening, Writing,
 * Speaking.
 *
 * Идемпотентен по названию внутри своего раздела: повторный прогон ничего
 * не дублирует и не переписывает уже существующие материалы. Вопросы к
 * тексту создаются только вместе с самим текстом — если текст уже был,
 * его вопросы не трогаются, иначе попытки учеников потеряли бы смысл.
 *
 * Содержимое лежит в database/seeders/data/ по файлу на раздел: восемьдесят
 * материалов с расшифровками и вопросами — это тысячи строк, и держать их
 * в одном классе было бы нечитаемо.
 */
class IeltsBatch3Seeder extends Seeder
{
    private array $report = ['reading' => 0, 'listening' => 0, 'writing' => 0, 'speaking' => 0];

    public function run(): void
    {
        $this->seedPassages(require __DIR__.'/data/ielts_reading.php', 'reading');
        $this->seedPassages(require __DIR__.'/data/ielts_listening.php', 'listening');
        $this->seedWriting(require __DIR__.'/data/ielts_writing.php');
        $this->seedSpeaking(require __DIR__.'/data/ielts_speaking.php');

        $this->command?->info(sprintf(
            'IeltsBatch3Seeder: +%d reading, +%d listening, +%d writing, +%d speaking',
            $this->report['reading'],
            $this->report['listening'],
            $this->report['writing'],
            $this->report['speaking'],
        ));
    }

    /** @param  array<int, array<string, mixed>>  $items */
    private function seedPassages(array $items, string $skill): void
    {
        foreach ($items as $item) {
            $exists = IeltsPassage::where('skill', $skill)
                ->where('title', $item['title'])
                ->exists();

            if ($exists) {
                continue;
            }

            $passage = IeltsPassage::create([
                'skill' => $skill,
                'title' => $item['title'],
                'level' => $item['level'],
                'passage_text' => $item['text'],
            ]);

            foreach ($item['questions'] as $order => $question) {
                $passage->questions()->create([
                    'question' => $question['q'],
                    'options' => $question['options'],
                    'correct_index' => $question['correct'],
                    'order_number' => $order,
                ]);
            }

            $this->report[$skill]++;
        }
    }

    /** @param  array<int, array<string, mixed>>  $items */
    private function seedWriting(array $items): void
    {
        foreach ($items as $item) {
            if (IeltsTask::where('title', $item['title'])->exists()) {
                continue;
            }

            IeltsTask::create([
                'type' => $item['type'],
                'title' => $item['title'],
                'prompt' => $item['prompt'],
                'chart_type' => $item['chart_type'] ?? null,
                'chart_data' => $item['chart_data'] ?? null,
                'topic' => $item['topic'] ?? null,
                'min_words' => $item['min_words'],
            ]);

            $this->report['writing']++;
        }
    }

    /** @param  array<int, array<string, mixed>>  $items */
    private function seedSpeaking(array $items): void
    {
        foreach ($items as $item) {
            if (IeltsSpeakingCard::where('title', $item['title'])->exists()) {
                continue;
            }

            IeltsSpeakingCard::create([
                'title' => $item['title'],
                'topic' => $item['topic'],
                'prompt' => $item['prompt'],
                'cue_points' => $item['cue_points'],
                // Part 2 всегда даёт минуту на подготовку и до двух минут
                // на ответ — это правила экзамена, а не наша настройка.
                'prep_seconds' => 60,
                'speak_seconds' => 120,
            ]);

            $this->report['speaking']++;
        }
    }
}
