<?php

namespace App\Services;

use App\Models\Placement\PlacementAnswer;
use App\Models\Placement\PlacementAttempt;
use App\Models\Placement\PlacementQuestion;
use App\Models\System\Level;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * Определяет уровень ученика по 50 вопросам теста, откалиброванным
 * по уровням A1-C2 (см. PlacementQuestionSeeder).
 *
 * Не берём просто сумму правильных ответов — иначе ученик, который
 * наугад дважды попал в C2, но провалил B1, оказался бы формально
 * «продвинутым». Вместо этого идём по уровням от A1 вверх и на каждом
 * считаем долю правильных ответов. Итоговый уровень — последний, на
 * котором доля не ниже порога PASS_THRESHOLD; как только уровень его
 * не проходит, выше не поднимаемся, даже если там случайно есть верные
 * ответы. Это стандартный принцип «потолка» для линейных (не
 * адаптивных) placement-тестов.
 */
class PlacementGradingService
{
    /** Публичная — используется и на странице результата для подсветки пройденных уровней. */
    public const PASS_THRESHOLD = 0.6;

    /** Порядок уровней от простого к сложному. */
    private const LEVEL_ORDER = ['A1', 'A2', 'B1', 'B2', 'C1', 'C2'];

    /**
     * @param  array<int, int|null>  $answers  [placement_question_id => placement_option_id|null]
     * @return array{attempt: PlacementAttempt, breakdown: array<int, array{code: string, correct: int, total: int, passed: bool}>}
     */
    public function grade(User $user, PlacementAttempt $attempt, array $answers): array
    {
        return DB::transaction(function () use ($user, $attempt, $answers) {
            $questions = PlacementQuestion::with(['options', 'level'])->orderBy('order_number')->get();

            $byLevel = [];
            $rows = [];

            foreach ($questions as $question) {
                $code = $question->level->code;
                $byLevel[$code] ??= ['correct' => 0, 'total' => 0];
                $byLevel[$code]['total']++;

                $selectedOptionId = $answers[$question->id] ?? null;
                $correctOption = $question->options->firstWhere('is_correct', true);
                $isCorrect = $selectedOptionId !== null && $correctOption && (int) $selectedOptionId === $correctOption->id;

                if ($isCorrect) {
                    $byLevel[$code]['correct']++;
                }

                $rows[] = [
                    'placement_attempt_id' => $attempt->id,
                    'placement_question_id' => $question->id,
                    'placement_option_id' => $selectedOptionId,
                    'is_correct' => $isCorrect,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            PlacementAnswer::insert($rows);

            // Идём по уровням от A1 вверх, считаем «потолок».
            $resultCode = null;
            $breakdown = [];

            foreach (self::LEVEL_ORDER as $code) {
                $stats = $byLevel[$code] ?? ['correct' => 0, 'total' => 0];
                $ratio = $stats['total'] > 0 ? $stats['correct'] / $stats['total'] : 0;
                $passed = $ratio >= self::PASS_THRESHOLD;

                $breakdown[] = [
                    'code' => $code,
                    'correct' => $stats['correct'],
                    'total' => $stats['total'],
                    'passed' => $passed,
                ];

                if ($passed) {
                    $resultCode = $code;
                } else {
                    // Потолок найден — выше не поднимаемся, даже если там
                    // случайно есть верные ответы.
                    break;
                }
            }

            // Не прошёл даже A1 — определяем по нему же, это самый низкий
            // уровень в системе, ниже опускаться некуда.
            $resultCode ??= self::LEVEL_ORDER[0];

            $resultLevel = Level::where('code', $resultCode)->orderBy('id')->first();

            $attempt->update([
                'status' => 'completed',
                'result_level_id' => $resultLevel->id,
                'completed_at' => now(),
            ]);

            // Ключевая часть задачи — не просто показать уровень, а
            // реально проставить его ученику, чтобы дальше по сайту
            // (бейдж «Без уровня», подбор уроков и т.п.) использовалось
            // актуальное значение.
            $user->update(['level_id' => $resultLevel->id]);

            return ['attempt' => $attempt->fresh(), 'breakdown' => $breakdown];
        });
    }
}
