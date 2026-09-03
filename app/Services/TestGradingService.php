<?php

namespace App\Services;

use App\Models\Test\Test;
use App\Models\Test\TestAttempt;
use App\Models\Test\TestQuestion;
use App\Models\User;
use App\Models\User\UserResult;
use Illuminate\Support\Facades\DB;

/**
 * Проверка теста и сохранение попытки.
 *
 * Пишем в две таблицы намеренно:
 *  - test_attempts + user_answers — подробности попытки, нужны для разбора
 *    ошибок и истории;
 *  - user_results — сводка, её уже читают дашборд, профиль и админка.
 * Переводить всех потребителей на test_attempts — отдельная задача,
 * а до тех пор расхождения между «сдал» в кабинете и историей быть не должно.
 */
class TestGradingService
{
    public function __construct(private readonly AchievementService $achievements) {}

    /**
     * @param  array<int|string, mixed>  $answers  ответы вида [question_id => значение]
     */
    public function grade(User $user, Test $test, array $answers, ?int $durationSeconds = null): TestAttempt
    {
        $test->loadMissing('questions.answers');

        return DB::transaction(function () use ($user, $test, $answers, $durationSeconds) {
            $maxPoints = 0;
            $earnedPoints = 0;
            $rows = [];

            foreach ($test->questions as $question) {
                $points = max(1, (int) $question->points);
                $maxPoints += $points;

                $given = $answers[$question->id] ?? null;
                $isCorrect = $this->isCorrect($question, $given);

                if ($isCorrect) {
                    $earnedPoints += $points;
                }

                $rows[] = [
                    'question_id' => $question->id,
                    'answer' => $this->answerToString($given),
                    'is_correct' => $isCorrect,
                ];
            }

            // Процент, а не сырые баллы: passing_score в тестах задан в процентах.
            $score = $maxPoints > 0 ? (int) round($earnedPoints / $maxPoints * 100) : 0;
            $passed = $score >= (int) $test->passing_score;

            $attempt = TestAttempt::create([
                'user_id' => $user->id,
                'test_id' => $test->id,
                'score' => $score,
                'passed' => $passed,
                'duration_seconds' => $durationSeconds,
                'started_at' => $durationSeconds ? now()->subSeconds($durationSeconds) : now(),
                'finished_at' => now(),
            ]);

            foreach ($rows as $row) {
                $attempt->answers()->create($row);
            }

            UserResult::create([
                'user_id' => $user->id,
                'test_id' => $test->id,
                'score' => $score,
                'passed' => $passed,
                'attempt_date' => now(),
            ]);

            if ($passed) {
                $this->achievements->checkAndAward($user, 'tests_passed');
            }

            return $attempt;
        });
    }

    /**
     * Вопрос считается верным, только если выбраны ВСЕ правильные варианты
     * и ни одного неправильного — иначе на multiple_choice можно было бы
     * отметить всё подряд и всегда получать балл.
     */
    private function isCorrect(TestQuestion $question, mixed $given): bool
    {
        if ($question->type === 'text') {
            $expected = $question->answers->where('is_correct', true)->pluck('answer');

            if ($expected->isEmpty() || ! is_string($given)) {
                return false;
            }

            $normalise = fn (string $value) => mb_strtolower(trim($value));

            return $expected->contains(fn ($answer) => $normalise($answer) === $normalise($given));
        }

        $correctIds = $question->answers->where('is_correct', true)->pluck('id')->map(fn ($id) => (int) $id)->sort()->values();
        $givenIds = collect(is_array($given) ? $given : (array) $given)
            ->filter(fn ($id) => is_numeric($id))
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->sort()
            ->values();

        return $correctIds->isNotEmpty() && $correctIds->all() === $givenIds->all();
    }

    private function answerToString(mixed $given): string
    {
        if (is_array($given)) {
            return implode(',', $given);
        }

        return $given === null ? '' : (string) $given;
    }
}
