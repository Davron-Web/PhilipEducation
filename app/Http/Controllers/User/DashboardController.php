<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Billing\Plan;
use App\Models\Content\Lesson;
use App\Models\Exercise\Exercise;
use App\Models\Gamification\Achievement;
use App\Models\Gamification\Title;
use App\Models\System\Level;
use App\Models\User;
use App\Models\User\UserProgress;
use App\Models\Vocabulary\Word;
use App\Services\PhilGreetingService;
use App\Services\SpacedRepetitionService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Display the user dashboard with progress stats.
     */
    public function index(SpacedRepetitionService $srs, PhilGreetingService $philGreetingService)
    {
        $user = Auth::user();
        $wordsDue = $srs->dueCount($user);

        $lessonsCompleted = $user->completedLessonsCount();
        $wordsLearned = $user->words()->wherePivot('learned', true)->count();
        $testsPassed = $user->results()->where('passed', true)->count();
        $achievementsCount = $user->achievements()->count();
        $exercisesSolved = $this->exercisesSolvedCount($user);

        $totalLessons = Lesson::where('is_published', true)->count();

        $stats = [
            'lessons_completed' => $lessonsCompleted,
            'lessons_progress' => $totalLessons ? round(min(100, ($lessonsCompleted / $totalLessons) * 100)) : 0,
            'words_learned' => $wordsLearned,
            'words_progress' => round(min(100, ($wordsLearned / 500) * 100)),
            'tests_passed' => $testsPassed,
            'tests_progress' => round(min(100, ($testsPassed / 20) * 100)),
            'exercises_solved' => $exercisesSolved,
            'achievements_count' => $achievementsCount,
            'achievements_total' => Achievement::count(),
            'hours_studied' => round($user->progress()->sum('time_spent') / 3600, 1),
            'streak' => $user->currentStreak(),
            'xp' => (int) $user->points,
        ];

        $title = $user->currentTitle();

        // Сколько осталось до следующего звания — прогресс виднее, чем голое
        // число опыта.
        $nextTitle = Title::where('is_active', true)
            ->where('min_xp', '>', $user->points)
            ->orderBy('min_xp')
            ->first();

        $recentXp = $user->xpEvents()->latest()->take(5)->get();

        // Последние полученные достижения (для карточки на дашборде) —
        // просто витрина, без выдуманного прогресса по недостающим: та
        // логика (честный мини-прогресс locked-достижений) осталась на
        // отдельной странице /achievements, здесь она была бы лишней.
        $recentAchievements = $user->achievements()
            ->orderByDesc('user_achievements.earned_at')
            ->take(4)
            ->get();

        // «Продолжить обучение»: сначала — последний урок, который начали,
        // но не закончили (порядок — по последнему обновлению прогресса,
        // а не по номеру урока: это то, над чем ученик реально работал
        // только что). Если такого нет — первый непройденный урок ЕГО
        // уровня (а не первый урок курса вообще — у ученика с уровнем B2
        // показывать A1 бессмысленно). Если и уровня нет — первый
        // непройденный урок курса по порядку, как и было раньше.
        $continueProgress = UserProgress::where('user_id', $user->id)
            ->where('is_completed', false)
            ->with('lesson.level')
            ->latest('updated_at')
            ->first();

        if ($continueProgress && $continueProgress->lesson) {
            $continueLesson = $continueProgress->lesson;
            $continueLessonProgress = $continueProgress->progress_percent;
        } else {
            $continueLesson = Lesson::where('is_published', true)
                ->whereDoesntHave('userProgress', function ($query) use ($user) {
                    $query->where('user_id', $user->id)->where('is_completed', true);
                })
                ->when($user->level_id, fn ($query) => $query->where('level_id', $user->level_id))
                ->orderBy(Level::select('code')->whereColumn('levels.id', 'lessons.level_id'))
                ->orderBy('order_number')
                ->first();

            // Уровень пользователя есть, но в нём почему-то не нашлось ни
            // одного непройденного урока (весь уровень уже пройден) —
            // тогда всё-таки берём первый непройденный урок курса, лишь
            // бы карточка не осталась пустой без реальной причины.
            if (! $continueLesson && $user->level_id) {
                $continueLesson = Lesson::where('is_published', true)
                    ->whereDoesntHave('userProgress', function ($query) use ($user) {
                        $query->where('user_id', $user->id)->where('is_completed', true);
                    })
                    ->orderBy(Level::select('code')->whereColumn('levels.id', 'lessons.level_id'))
                    ->orderBy('order_number')
                    ->first();
            }

            $continueLessonProgress = 0;
        }

        // Приветствие Phil — на английском, под уровень ученика, кэшируется
        // на сутки внутри самого сервиса (см. PhilGreetingService::greetingFor()).
        $philGreeting = $philGreetingService->greetingFor($user, $continueLesson);

        $dailyExercise = Exercise::whereDoesntHave('userAnswers', function ($query) use ($user) {
                $query->where('user_id', $user->id)->whereDate('created_at', today());
            })
            ->orderBy('id')
            ->first();

        // Слово дня: один и тот же для всех и весь день (детерминированный
        // выбор по дню года), а не случайный при каждом обновлении страницы —
        // иначе это было бы "слово момента", а не "слово дня".
        $wordOfDay = $this->wordOfTheDay();

        $streakDays = $this->lastSevenDays($user);

        $plans = $this->pricingPlans($user);

        return view('user.dashboard', compact(
            'stats',
            'continueLesson',
            'continueLessonProgress',
            'philGreeting',
            'dailyExercise',
            'wordOfDay',
            'streakDays',
            'wordsDue',
            'title',
            'nextTitle',
            'recentXp',
            'recentAchievements',
            'plans'
        ));
    }

    /**
     * Витрина тарифов на дашборде: статичные буллеты и названия — из
     * config/plans.php, цена и валюта — реальные, из billing (App\Models\
     * Billing\Plan). В проекте один платный уровень доступа, поэтому PRO
     * и PREMIUM ссылаются на разные тарифы биллинга (monthly/yearly) —
     * это про длительность подписки, а не про разный набор прав.
     */
    private function pricingPlans(User $user): array
    {
        $billingPlans = Plan::where('is_active', true)->get()->keyBy('code');
        $isSubscribed = $user->hasActiveSubscription();

        return collect(config('plans'))->map(function ($plan, $key) use ($billingPlans, $isSubscribed) {
            if ($plan['plan_code'] && $billingPlan = $billingPlans->get($plan['plan_code'])) {
                $plan['price_label'] = number_format($billingPlan->price, 0, ',', ' ').' '.$billingPlan->currency;
                $plan['period_label'] = $billingPlan->code === 'yearly' ? 'в год' : 'в месяц';
            }

            $plan['is_current'] = $key === 'free' ? ! $isSubscribed : $isSubscribed;

            return $plan;
        })->all();
    }

    /**
     * Сколько упражнений ученик действительно решил — ответил на все
     * вопросы упражнения хотя бы раз (не обязательно правильно: "решено"
     * здесь значит "пройдено целиком", как и everywhere на сайте считается
     * прогресс, а не "все ответы верны").
     */
    private function exercisesSolvedCount(User $user): int
    {
        return DB::table('user_exercise_answers')
            ->where('user_id', $user->id)
            ->select('exercise_id', DB::raw('COUNT(DISTINCT question_id) as answered'))
            ->groupBy('exercise_id')
            ->get()
            ->filter(function ($row) {
                $total = DB::table('exercise_questions')->where('exercise_id', $row->exercise_id)->count();

                return $total > 0 && $row->answered >= $total;
            })
            ->count();
    }

    /**
     * Слово дня — детерминированный выбор по номеру дня в году, чтобы все
     * видели одно и то же слово весь день и оно менялось каждый день, без
     * отдельного поля "featured" в БД (такого поля у слов нет).
     */
    private function wordOfTheDay(): ?Word
    {
        $total = Word::count();

        if ($total === 0) {
            return null;
        }

        $index = (now()->dayOfYear + now()->year) % $total;

        return Word::with('translations')->orderBy('id')->skip($index)->take(1)->first();
    }

    /**
     * Последние 7 дней (сегодня — последний) с отметкой, был ли в этот
     * день завершён хотя бы один урок — для полоски дней streak на дашборде.
     *
     * @return array<int, array{label: string, active: bool, isToday: bool}>
     */
    private function lastSevenDays(User $user): array
    {
        $completedDates = $user->progress()
            ->whereNotNull('completed_at')
            ->pluck('completed_at')
            ->map(fn ($date) => $date->toDateString())
            ->unique();

        $days = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $days[] = [
                'label' => $date->translatedFormat('D'),
                'active' => $completedDates->contains($date->toDateString()),
                'isToday' => $i === 0,
            ];
        }

        return $days;
    }
}
