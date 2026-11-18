<?php

namespace Database\Seeders;

use App\Models\Content\Lesson;
use App\Models\Gamification\Achievement;
use App\Models\Gamification\StudyStatistic;
use App\Models\User;
use App\Models\User\Role;
use App\Models\User\UserAchievement;
use App\Models\User\UserProgress;
use App\Models\User\UserResult;
use App\Services\XpService;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

/**
 * Generates fake student accounts (email domain @example.com) with
 * realistic learning activity: progress, test results, learned words,
 * achievements, and study statistics. Idempotent — safe to re-run;
 * only creates users that don't already exist (checked by email) and
 * processes them in batches of $batchSize per invocation.
 *
 * Размер набора и порции задаются переменными окружения FAKE_USERS_TOTAL
 * и FAKE_USERS_BATCH (по умолчанию 100 и 10).
 */
class FakeUsersSeeder extends Seeder
{
    private int $totalTarget;

    private int $batchSize;

    public function __construct(private readonly XpService $xpService)
    {
        $this->totalTarget = (int) env('FAKE_USERS_TOTAL', 100);
        $this->batchSize = (int) env('FAKE_USERS_BATCH', 10);
    }

    private array $firstNames = [
        'Anna', 'Timur', 'Maria', 'John', 'Elena', 'Rustam', 'Sofia', 'David',
        'Olga', 'Aziz', 'Laura', 'Michael', 'Diana', 'Sardor', 'Emma', 'James',
        'Nigora', 'Alex', 'Kamila', 'Daniel', 'Yulia', 'Bekzod', 'Grace', 'Robert',
        'Malika', 'Farrukh', 'Natalie', 'William', 'Zarina', 'Umid', 'Sara', 'Thomas',
        'Dilnoza', 'Jasur', 'Victoria', 'Andrew', 'Gulnora', 'Shakhzod', 'Chloe', 'Kevin',
        'Madina', 'Otabek', 'Julia', 'Peter', 'Feruza', 'Bobur', 'Hannah', 'Richard',
        'Sabina', 'Islom',
    ];

    private array $lastNames = [
        'Sokolova', 'Aliyev', 'Kim', 'Carter', 'Petrova', 'Yusupov', 'Johnson', 'Smith',
        'Ivanova', 'Karimov', 'Williams', 'Brown', 'Rakhimova', 'Nazarov', 'Davis', 'Miller',
        'Yusupova', 'Tashkentov', 'Wilson', 'Moore', 'Abdullayeva', 'Rustamov', 'Taylor', 'Anderson',
        'Yuldasheva', 'Ergashev', 'Thomas', 'Jackson', 'Sharipova', 'Xolmatov', 'White', 'Harris',
        'Nabieva', 'Saidov', 'Martin', 'Thompson', 'Ismoilova', 'Tursunov', 'Garcia', 'Martinez',
        'Yunusova', 'Qodirov', 'Robinson', 'Clark', 'Otabekova', 'Mirzayev', 'Lewis', 'Walker',
        'Rashidova', 'Bekmuratov',
    ];

    /** @var array<string,mixed> */
    private array $report = [
        'users' => 0, 'progress' => 0, 'results' => 0, 'words' => 0,
        'achievements' => 0, 'study_stats' => 0, 'errors' => [],
        'skipped_tables' => [],
    ];

    public function run(): void
    {
        $studentRole = Role::firstOrCreate(['name' => 'student'], ['description' => 'Ученик платформы']);

        $plannedUsers = $this->buildPlannedUserList();

        $existingEmails = User::whereIn('email', array_column($plannedUsers, 'email'))
            ->pluck('email')->flip();

        $toCreate = array_values(array_filter(
            $plannedUsers,
            fn ($u) => ! isset($existingEmails[$u['email']])
        ));

        $batch = array_slice($toCreate, 0, $this->batchSize);

        if (empty($batch)) {
            $this->command?->info('FakeUsersSeeder: nothing to do — all planned users already exist.');
            $this->printFinalReport();

            return;
        }

        $lessons = Lesson::with(['tests', 'words'])->orderBy('level_id')->orderBy('order_number')->get();
        $achievementIds = Achievement::pluck('id', 'title');

        foreach ($batch as $planned) {
            $this->createFakeUser($planned, $studentRole->id, $lessons, $achievementIds);
        }

        $this->command?->info(sprintf(
            'FakeUsersSeeder batch: +%d users, +%d progress, +%d results, +%d words, +%d achievements, +%d study_stats (remaining after this batch: %d)',
            $this->report['users'],
            $this->report['progress'],
            $this->report['results'],
            $this->report['words'],
            $this->report['achievements'],
            $this->report['study_stats'],
            max(0, count($toCreate) - count($batch))
        ));

        foreach ($this->report['errors'] as $error) {
            $this->command?->error($error);
        }
    }

    /**
     * @return array<int, array{name:string, email:string, created_at: Carbon, recent: bool}>
     */
    private function buildPlannedUserList(): array
    {
        mt_srand(42); // deterministic across repeated invocations

        $planned = [];
        $usedEmails = [];
        $recentCount = 0;
        $maxRecent = max(5, (int) round($this->totalTarget * 0.13));

        for ($i = 0; $i < $this->totalTarget; $i++) {
            $first = $this->firstNames[$i % count($this->firstNames)];
            $last = $this->lastNames[($i * 7 + intdiv($i, count($this->firstNames))) % count($this->lastNames)];
            $name = "{$first} {$last}";

            $slug = Str::slug($first).'.'.Str::slug($last);
            $email = "{$slug}{$i}@example.com";
            $usedEmails[$email] = true;

            $isRecent = $recentCount < $maxRecent && ($i % 7 === 0);
            if ($isRecent) {
                $recentCount++;
                $createdAt = now()->subDays(random_int(0, 7))->subMinutes(random_int(0, 1440));
            } else {
                $createdAt = now()->subDays(random_int(8, 182))->subMinutes(random_int(0, 1440));
            }

            $planned[] = [
                'name' => $name,
                'email' => $email,
                'created_at' => $createdAt,
                'recent' => $isRecent,
            ];
        }

        mt_srand(); // reseed randomly for the actual data-generation randomness below

        return $planned;
    }

    private function createFakeUser(array $planned, int $roleId, $lessons, $achievementIds): void
    {
        try {
            DB::transaction(function () use ($planned, $roleId, $lessons, $achievementIds) {
                $user = User::create([
                    'name' => $planned['name'],
                    'email' => $planned['email'],
                    'password' => Hash::make('password1234'),
                    'role_id' => $roleId,
                    'is_active' => true,
                ]);
                User::where('id', $user->id)->update(['created_at' => $planned['created_at'], 'updated_at' => $planned['created_at']]);
                $this->report['users']++;

                $tierRoll = random_int(1, 100);
                $lessonCount = match (true) {
                    $tierRoll <= 20 => random_int(1, 3),   // 20% novice
                    $tierRoll <= 70 => random_int(4, 15),  // 50% intermediate
                    default => random_int(16, 40),         // 30% advanced
                };
                $lessonCount = min($lessonCount, $lessons->count());

                $inProgressCount = min(random_int(1, 2), $lessons->count() - $lessonCount);
                $inProgressCount = max(0, $inProgressCount);

                $completedLessons = $lessons->slice(0, $lessonCount)->values();
                $inProgressLessons = $lessons->slice($lessonCount, $inProgressCount)->values();

                $now = now();
                $spanSinceCreation = max(60, (int) abs($planned['created_at']->diffInMinutes($now)));

                if ($planned['recent']) {
                    $lastActiveAt = $now->copy()->subHours(random_int(1, 47));
                } else {
                    // Land somewhere in the middle of their history, never within the
                    // last 3 days, so only the explicitly "recent" cohort looks fresh today.
                    $minOffset = min($spanSinceCreation, 3 * 24 * 60);
                    $offset = random_int($minOffset, $spanSinceCreation);
                    $lastActiveAt = $now->copy()->subMinutes($offset);
                }
                if ($lastActiveAt->lt($planned['created_at'])) {
                    $lastActiveAt = $planned['created_at']->copy()->addHours(random_int(1, 48));
                }

                $totalSpan = max(1, (int) abs($planned['created_at']->diffInMinutes($lastActiveAt)));
                $hasRetryBudget = random_int(1, 100) <= 20;
                $usedRetry = false;

                $wordsLearnedTotal = 0;
                $timeSpentTotal = 0;
                $testsPassedTotal = 0;

                // Для начисления опыта через XpService: id тестов, которые в
                // итоге сданы (по одному разу — повторная сдача того же
                // теста опыт не удваивает, как и в боевом коде), и сколько
                // слов выучено в каждом уроке (одно агрегированное начисление
                // на урок, а не по слову — иначе на «продвинутого» ученика
                // выходили бы сотни отдельных начислений).
                $passedTestIds = [];
                $wordsLearnedByLesson = [];

                foreach ($completedLessons as $index => $lesson) {
                    $fraction = ($index + 1) / max(1, $completedLessons->count() + $inProgressCount);
                    $activityAt = $planned['created_at']->copy()->addMinutes((int) round($totalSpan * $fraction))
                        ->addMinutes(random_int(-30, 30));
                    if ($activityAt->lt($planned['created_at'])) {
                        $activityAt = $planned['created_at']->copy()->addMinutes(random_int(5, 120));
                    }
                    if ($activityAt->gt($now)) {
                        $activityAt = $now->copy()->subMinutes(random_int(1, 60));
                    }

                    $timeSpent = random_int(300, 2400); // 5–40 minutes in seconds
                    $timeSpentTotal += $timeSpent;

                    UserProgress::create([
                        'user_id' => $user->id,
                        'lesson_id' => $lesson->id,
                        'progress_percent' => 100,
                        'is_completed' => true,
                        'time_spent' => $timeSpent,
                        'last_position' => 0,
                        'completed_at' => $activityAt,
                        'created_at' => $activityAt->copy()->subMinutes(random_int(5, 30)),
                        'updated_at' => $activityAt,
                    ]);
                    $this->report['progress']++;

                    $test = $lesson->tests->first();
                    if ($test) {
                        $attemptAt = $activityAt->copy()->addMinutes(random_int(2, 45));
                        if ($attemptAt->gt($now)) {
                            $attemptAt = $now->copy();
                        }

                        $score = $this->rollScore();
                        $passed = $score >= $test->passing_score;

                        UserResult::create([
                            'user_id' => $user->id,
                            'test_id' => $test->id,
                            'score' => $score,
                            'passed' => $passed,
                            'attempt_date' => $attemptAt,
                            'created_at' => $attemptAt,
                            'updated_at' => $attemptAt,
                        ]);
                        $this->report['results']++;
                        if ($passed) {
                            $testsPassedTotal++;
                            $passedTestIds[$test->id] = true;
                        }

                        if (! $passed && $hasRetryBudget && ! $usedRetry) {
                            $usedRetry = true;
                            $retryAt = $attemptAt->copy()->addHours(random_int(2, 30));
                            if ($retryAt->gt($now)) {
                                $retryAt = $now->copy();
                            }
                            $retryScore = random_int($test->passing_score, 98);

                            UserResult::create([
                                'user_id' => $user->id,
                                'test_id' => $test->id,
                                'score' => $retryScore,
                                'passed' => true,
                                'attempt_date' => $retryAt,
                                'created_at' => $retryAt,
                                'updated_at' => $retryAt,
                            ]);
                            $this->report['results']++;
                            $testsPassedTotal++;
                            $passedTestIds[$test->id] = true;
                        }
                    }

                    if ($lesson->words->isNotEmpty()) {
                        $wordRows = [];
                        $learnedInLesson = 0;
                        foreach ($lesson->words as $word) {
                            $learned = (random_int(1, 100) <= random_int(50, 90));
                            $correct = $learned ? random_int(3, 10) : random_int(0, 3);
                            $wrong = $learned ? random_int(0, 3) : random_int(1, 4);

                            $wordRows[] = [
                                'user_id' => $user->id,
                                'word_id' => $word->id,
                                'learned' => $learned,
                                'correct_answers' => $correct,
                                'wrong_answers' => $wrong,
                                'last_reviewed_at' => $activityAt,
                                'created_at' => $activityAt,
                                'updated_at' => $activityAt,
                            ];

                            if ($learned) {
                                $wordsLearnedTotal++;
                                $learnedInLesson++;
                            }
                        }

                        if (! empty($wordRows)) {
                            DB::table('user_words')->insert($wordRows);
                            $this->report['words'] += count($wordRows);
                        }

                        if ($learnedInLesson > 0) {
                            $wordsLearnedByLesson[$lesson->id] = $learnedInLesson;
                        }
                    }
                }

                foreach ($inProgressLessons as $lesson) {
                    $activityAt = $lastActiveAt->copy()->subMinutes(random_int(0, 500));
                    if ($activityAt->lt($planned['created_at'])) {
                        $activityAt = $planned['created_at']->copy()->addMinutes(random_int(5, 120));
                    }

                    UserProgress::create([
                        'user_id' => $user->id,
                        'lesson_id' => $lesson->id,
                        'progress_percent' => random_int(30, 90),
                        'is_completed' => false,
                        'time_spent' => random_int(120, 1200),
                        'last_position' => random_int(1, 5),
                        'completed_at' => null,
                        'created_at' => $activityAt->copy()->subMinutes(random_int(5, 30)),
                        'updated_at' => $activityAt,
                    ]);
                    $this->report['progress']++;
                }

                $completedCount = $completedLessons->count();

                $achievementMilestones = [
                    'First Lesson' => $completedCount >= 1,
                    '5 Lessons Completed' => $completedCount >= 5,
                    '10 Lessons Completed' => $completedCount >= 10,
                    'First Test Passed' => $testsPassedTotal >= 1,
                    'Persistent Learner' => $completedCount >= 20,
                ];

                foreach ($achievementMilestones as $title => $earned) {
                    if (! $earned || ! isset($achievementIds[$title])) {
                        continue;
                    }

                    UserAchievement::create([
                        'user_id' => $user->id,
                        'achievement_id' => $achievementIds[$title],
                        'earned_at' => $lastActiveAt,
                    ]);
                    $this->report['achievements']++;
                }

                if (Schema::hasTable('study_statistics')) {
                    StudyStatistic::create([
                        'user_id' => $user->id,
                        'total_lessons_completed' => $completedCount,
                        'total_tests_passed' => $testsPassedTotal,
                        'total_words_learned' => $wordsLearnedTotal,
                        'study_time_minutes' => (int) round($timeSpentTotal / 60),
                    ]);
                    $this->report['study_stats']++;
                } elseif (! in_array('study_statistics', $this->report['skipped_tables'], true)) {
                    $this->report['skipped_tables'][] = 'study_statistics';
                }

                // last_login_at не заполнялся вовсе, и метрика «активные
                // пользователи» в админке показывала почти ноль при 600 учениках.
                User::where('id', $user->id)->update(['last_login_at' => $lastActiveAt]);

                // points раньше писались в обход журнала — фейковые ученики
                // существовали только в рейтинге «за всё время» (он единственный
                // читает users.points напрямую) и не попадали в «неделя»/«месяц»
                // (те построены на xp_events). Теперь начисление идёт через
                // XpService::award(), как и для настоящих учеников — сумма
                // xp_events для пользователя равна его users.points по построению
                // (award() инкрементирует points тем же числом, что пишет в журнал).
                $this->awardFakeActivityXp(
                    $user,
                    $planned,
                    $completedLessons,
                    $passedTestIds,
                    $wordsLearnedByLesson,
                    $achievementMilestones,
                    $achievementIds
                );
            });
        } catch (\Throwable $e) {
            $this->report['errors'][] = "{$planned['email']}: {$e->getMessage()}";
        }
    }

    /**
     * Начисляет опыт за уже сгенерированную активность пользователя через
     * XpService — по одному начислению на пройденный урок, на сданный тест
     * (уникально — id теста тот же, что и в бою, задваивания при пересдаче
     * не будет) и на достижение, плюс агрегированное начисление за слова,
     * выученные в каждом уроке.
     *
     * Даты начислений — случайные в пределах последних 60 дней, а не даты
     * самих уроков/тестов (которые для «старых» аккаунтов могут уходить на
     * полгода назад): иначе периоды «неделя» и «месяц» рейтинга опять были
     * бы пустыми почти для всех фейковых учеников.
     *
     * @param  array{created_at: Carbon}  $planned
     * @param  Collection<int, \App\Models\Content\Lesson>  $completedLessons
     * @param  array<int, true>  $passedTestIds  id теста => true
     * @param  array<int, int>  $wordsLearnedByLesson  id урока => сколько слов выучено
     * @param  array<string, bool>  $achievementMilestones  название достижения => заработано ли
     * @param  Collection<string, int>  $achievementIds  название достижения => id
     */
    private function awardFakeActivityXp(
        User $user,
        array $planned,
        Collection $completedLessons,
        array $passedTestIds,
        array $wordsLearnedByLesson,
        array $achievementMilestones,
        Collection $achievementIds
    ): void {
        $windowFloor = now()->subDays(60);
        $windowStart = $planned['created_at']->gt($windowFloor) ? $planned['created_at']->copy() : $windowFloor->copy();
        $windowEnd = now();

        foreach ($completedLessons as $lesson) {
            $this->awardFakeXp($user, 'lesson', $lesson->id, null, $windowStart, $windowEnd);
        }

        foreach (array_keys($passedTestIds) as $testId) {
            $this->awardFakeXp($user, 'test', $testId, null, $windowStart, $windowEnd);
        }

        foreach ($wordsLearnedByLesson as $lessonId => $learnedCount) {
            $this->awardFakeXp($user, 'word', $lessonId, $learnedCount * XpService::REWARDS['word'], $windowStart, $windowEnd);
        }

        foreach ($achievementMilestones as $title => $earned) {
            if (! $earned || ! isset($achievementIds[$title])) {
                continue;
            }

            $achievementId = $achievementIds[$title];
            $points = Achievement::find($achievementId)?->points;

            $this->awardFakeXp($user, 'achievement', $achievementId, $points > 0 ? $points : null, $windowStart, $windowEnd);
        }
    }

    /** Начисляет через XpService и сразу переносит начисление на случайную дату в заданном окне. */
    private function awardFakeXp(User $user, string $source, int $sourceId, ?int $amount, Carbon $windowStart, Carbon $windowEnd): void
    {
        $granted = $this->xpService->award($user, $source, $sourceId, $amount);

        if ($granted === 0) {
            return;
        }

        DB::table('xp_events')
            ->where('user_id', $user->id)
            ->where('source', $source)
            ->where('source_id', $sourceId)
            ->update([
                'created_at' => $this->randomDateBetween($windowStart, $windowEnd),
                'updated_at' => now(),
            ]);
    }

    private function randomDateBetween(Carbon $start, Carbon $end): Carbon
    {
        if ($start->gte($end)) {
            return $end->copy();
        }

        return $start->copy()->addSeconds(random_int(0, (int) $start->diffInSeconds($end)));
    }

    private function rollScore(): int
    {
        $roll = random_int(1, 100);

        if ($roll <= 15) {
            return random_int(45, 74); // ~15% fail bucket (passing_score = 75)
        }

        if ($roll <= 90) {
            return random_int(75, 95); // main pass bucket, stays clear of the fail line
        }

        return random_int(96, 100); // occasional near-perfect
    }

    private function printFinalReport(): void
    {
        $total = User::where('email', 'like', '%@example.com')->count();
        $this->command?->info("Total fake users so far: {$total} / {$this->totalTarget}");
    }
}
