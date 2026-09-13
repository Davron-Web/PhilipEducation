<?php

namespace App\Services;

use App\Models\Certificate\Certificate;
use App\Models\Content\Lesson;
use App\Models\System\Level;
use App\Models\Test\Test;
use App\Models\User;
use App\Models\User\UserResult;
use App\Notifications\CertificateEarned;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Прогресс и автоматическая выдача сертификатов уровня. progressFor()
 * показывает три условия по каждому уровню отдельно (уроки/тесты/слова),
 * а не один общий процент, чтобы было видно, чего именно не хватает.
 * checkAndIssue() проверяет готовность через ЭТОТ ЖЕ метод, а не
 * пересчитывает условия во второй раз — что видно на /achievements,
 * то и решает, выдавать ли сертификат.
 */
class CertificateService
{
    /**
     * Уровень доступен для сертификации, если в нём вообще есть
     * опубликованные уроки и тесты — общее правило, а не проверка «это
     * C2»: любой будущий пустой уровень так же попадёт в «Скоро».
     */
    public function isLevelAvailable(Level $level): bool
    {
        return Lesson::where('level_id', $level->id)->where('is_published', true)->exists()
            && Test::where('is_published', true)
                ->whereHas('lesson', fn ($q) => $q->where('level_id', $level->id))
                ->exists();
    }

    /**
     * Прогресс пользователя по всем шести уровням CEFR — по каждому
     * условию отдельно (уроки / тесты со средним баллом / слова).
     *
     * @return array<int, array<string, mixed>>
     */
    public function progressFor(User $user): array
    {
        $thresholds = config('certificates.word_thresholds');
        $requiredAverage = config('certificates.passing_average_score');

        // Слова считаются той же формулой, что и достижение «Мастер слов»
        // (AchievementService::currentCountFor('words_learned')) — без
        // фильтра по уровню, чтобы цифра не расходилась между страницами.
        $wordsLearned = $user->words()->wherePivot('learned', true)->count();

        $earnedByLevelId = Certificate::where('user_id', $user->id)->get()->keyBy('level_id');

        $levels = Level::whereIn('code', ['A1', 'A2', 'B1', 'B2', 'C1', 'C2'])
            ->orderBy('code')
            ->get();

        return $levels->map(function (Level $level) use ($user, $thresholds, $requiredAverage, $wordsLearned, $earnedByLevelId) {
            $publishedLessons = Lesson::where('level_id', $level->id)->where('is_published', true)->count();
            $publishedTests = Test::where('is_published', true)
                ->whereHas('lesson', fn ($q) => $q->where('level_id', $level->id))
                ->count();

            $isAvailable = $publishedLessons > 0 && $publishedTests > 0;

            $completedLessons = $isAvailable
                ? $user->progress()
                    ->where('is_completed', true)
                    ->whereHas('lesson', fn ($q) => $q->where('level_id', $level->id))
                    ->count()
                : 0;

            // Лучший (MAX) счёт среди сданных попыток на каждый тест
            // уровня — пересдачи не портят средний неудачными черновыми
            // попытками, тот же принцип, что и в подсчёте tests_passed
            // у достижений (там — distinct по test_id, здесь — MAX(score)
            // на test_id, задача уже сложнее одного count()).
            $bestScoresPerTest = $isAvailable
                ? UserResult::where('user_id', $user->id)
                    ->where('passed', true)
                    ->whereHas('test', fn ($q) => $q->where('is_published', true)->whereHas('lesson', fn ($q2) => $q2->where('level_id', $level->id)))
                    ->selectRaw('test_id, MAX(score) as best_score')
                    ->groupBy('test_id')
                    ->get()
                : collect();

            $passedTests = $bestScoresPerTest->count();
            $avgScore = $passedTests > 0 ? (int) round($bestScoresPerTest->avg('best_score')) : 0;

            $wordsTarget = $thresholds[$level->code] ?? 0;

            $lessonsComplete = $publishedLessons > 0 && $completedLessons >= $publishedLessons;
            $testsComplete = $publishedTests > 0 && $passedTests >= $publishedTests && $avgScore >= $requiredAverage;
            $wordsComplete = $wordsTarget > 0 && $wordsLearned >= $wordsTarget;

            $testsRatio = $publishedTests > 0
                ? min($passedTests / $publishedTests, $avgScore > 0 ? min(1, $avgScore / $requiredAverage) : 0)
                : 0;

            $certificate = $earnedByLevelId->get($level->id);

            return [
                'code' => $level->code,
                'name' => $level->name,
                'level_id' => $level->id,
                'is_earned' => (bool) $certificate,
                'certificate' => $certificate,
                'is_available' => $isAvailable,
                'is_complete' => $lessonsComplete && $testsComplete && $wordsComplete,
                'lessons' => [
                    'current' => min($completedLessons, $publishedLessons),
                    'target' => $publishedLessons,
                    'percent' => $publishedLessons > 0 ? (int) round(min(1, $completedLessons / $publishedLessons) * 100) : 0,
                    'complete' => $lessonsComplete,
                ],
                'tests' => [
                    'current' => $passedTests,
                    'target' => $publishedTests,
                    'avg_score' => $avgScore,
                    'required_score' => $requiredAverage,
                    'percent' => (int) round($testsRatio * 100),
                    'complete' => $testsComplete,
                ],
                'words' => [
                    'current' => min($wordsLearned, $wordsTarget),
                    'target' => $wordsTarget,
                    'percent' => $wordsTarget > 0 ? (int) round(min(1, $wordsLearned / $wordsTarget) * 100) : 0,
                    'complete' => $wordsComplete,
                ],
            ];
        })->all();
    }

    /**
     * Выдаёт сертификаты по всем уровням, условия которых сейчас выполнены
     * и ещё не выданы — вызывается после действий, которые могут закрыть
     * условие уровня (урок пройден, тест сдан, слово выучено), тем же
     * принципом, что и AchievementService::checkAndAward(). Не пересчитывает
     * условия по-своему: use-cases всегда одни и те же три условия из
     * progressFor(), поэтому расхождения между тем, что видно на
     * /achievements, и тем, что выдаётся, здесь невозможны.
     *
     * Также используется command certificates:backfill — для пользователей,
     * которые выполнили условия уровня ДО того, как появилась автовыдача.
     *
     * @return array<int, Certificate> новые сертификаты (для уведомления/отчёта)
     */
    public function checkAndIssue(User $user): array
    {
        $issued = [];

        foreach ($this->progressFor($user) as $level) {
            if ($level['is_available'] && $level['is_complete'] && ! $level['is_earned']) {
                $certificate = $this->issue($user, $level['level_id'], $level['code']);

                if ($certificate) {
                    $issued[] = $certificate;
                }
            }
        }

        return $issued;
    }

    /**
     * certificate_number обязателен и уникален уже на INSERT, а собрать его
     * хочется из id самой записи (CERT-B1-000123) — поэтому создаём с
     * временным уникальным значением (uuid) и сразу поправляем.
     *
     * Составной unique(user_id, level_id) в БД — подстраховка от гонки
     * (два параллельных запроса одновременно проходят проверку is_earned):
     * если он сработал, второй вызов тихо возвращает null, а не падает
     * 500-й ошибкой пользователю.
     */
    private function issue(User $user, int $levelId, string $levelCode): ?Certificate
    {
        try {
            return DB::transaction(function () use ($user, $levelId, $levelCode) {
                $certificate = Certificate::create([
                    'user_id' => $user->id,
                    'level_id' => $levelId,
                    'certificate_number' => (string) Str::uuid(),
                    'issued_at' => now(),
                ]);

                $certificate->update([
                    'certificate_number' => sprintf('CERT-%s-%06d', $levelCode, $certificate->id),
                ]);

                $user->notify(new CertificateEarned($certificate));

                return $certificate;
            });
        } catch (QueryException) {
            return null;
        }
    }
}
