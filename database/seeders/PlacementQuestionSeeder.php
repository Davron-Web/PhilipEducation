<?php

namespace Database\Seeders;

use App\Models\Placement\PlacementQuestion;
use App\Models\System\Level;
use Illuminate\Database\Seeder;

/**
 * 50 вопросов теста на определение уровня — по данным из
 * database/seeders/data/placement_questions.php.
 *
 * Идемпотентен по тексту вопроса: повторный прогон не создаёт дублей.
 * Уровни ищутся по code (A1..C2) — если в таблице levels заведена
 * лишняя запись с тем же code (так уже бывало от ручного тестирования
 * админки), берётся первая по id, чтобы сидер не размножал вопросы.
 */
class PlacementQuestionSeeder extends Seeder
{
    public function run(): void
    {
        $levelIds = Level::query()
            ->whereIn('code', ['A1', 'A2', 'B1', 'B2', 'C1', 'C2'])
            ->orderBy('id')
            ->get()
            ->unique('code')
            ->pluck('id', 'code');

        $data = require __DIR__.'/data/placement_questions.php';
        $created = 0;
        $skipped = 0;

        foreach ($data as $code => $questions) {
            $levelId = $levelIds[$code] ?? null;

            if (! $levelId) {
                $this->command?->error("PlacementQuestionSeeder: уровень {$code} не найден в таблице levels, пропущено {$code}.");

                continue;
            }

            foreach ($questions as $order => $item) {
                if (PlacementQuestion::where('question', $item['q'])->exists()) {
                    $skipped++;

                    continue;
                }

                $question = PlacementQuestion::create([
                    'level_id' => $levelId,
                    'question' => $item['q'],
                    'order_number' => $order + 1,
                ]);

                foreach ($item['options'] as $i => $text) {
                    $question->options()->create([
                        'text' => $text,
                        'is_correct' => $i === $item['correct'],
                        'sort_order' => $i,
                    ]);
                }

                $created++;
            }
        }

        $this->command?->info("PlacementQuestionSeeder: +{$created} вопросов, {$skipped} уже существовало.");
    }
}
