<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Billing\Invoice;
use App\Models\Billing\Subscription;
use App\Models\Content\Lesson;
use App\Models\User;
use App\Models\User\UserProgress;
use App\Models\User\UserResult;
use App\Models\Vocabulary\Word;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

/**
 * Сводка для администратора.
 *
 * Раньше цифры считались прямо в Blade-шаблоне: запросы к базе шли из
 * разметки, покрыть их тестом было нечем, а массив $stats из контроллера
 * с зашитыми «+12%» и 8540 шаблон просто не читал. Теперь всё считается
 * здесь, а шаблон только отображает.
 */
class DashboardController extends Controller
{
    private const MONTHS = ['Янв', 'Фев', 'Мар', 'Апр', 'Май', 'Июн', 'Июл', 'Авг', 'Сен', 'Окт', 'Ноя', 'Дек'];

    public function index(): View
    {
        return view('admin.dashboard', [
            ...$this->platformStats(),
            ...$this->billingStats(),
            ...$this->charts(),
            ...$this->recentActivity(),
        ]);
    }

    /** @return array<string, mixed> */
    private function platformStats(): array
    {
        return [
            'usersCount' => User::count(),
            'wordsCount' => Word::count(),
            'lessonsDone' => UserProgress::where('is_completed', true)->count(),
            'avgScore' => (int) round(UserResult::avg('score') ?? 0),

            // Активными считаем тех, кто заходил за последние 30 дней:
            // last_login_at заполняется при входе.
            'activeUsers' => User::where('last_login_at', '>=', now()->subDays(30))->count(),
            'newUsers' => User::where('created_at', '>=', now()->subDays(30))->count(),
        ];
    }

    /**
     * Деньги и подписки.
     *
     * Доход берём из счетов, а не из платежей: счёт выписывается только по
     * факту успешной оплаты, поэтому неудачные и висящие попытки в сумму
     * не попадают.
     *
     * @return array<string, mixed>
     */
    private function billingStats(): array
    {
        $activeSubscriptions = Subscription::whereIn('status', [
                Subscription::STATUS_ACTIVE,
                Subscription::STATUS_CANCELLED,
            ])
            ->where(fn ($q) => $q->whereNull('ends_at')->orWhere('ends_at', '>', now()))
            ->distinct()
            ->count('user_id');

        return [
            'proUsers' => $activeSubscriptions,
            'freeUsers' => max(0, User::count() - $activeSubscriptions),
            'revenueTotal' => (int) Invoice::where('status', Invoice::STATUS_PAID)->sum('amount_minor'),
            'revenueMonth' => (int) Invoice::where('status', Invoice::STATUS_PAID)
                ->where('issued_at', '>=', now()->startOfMonth())
                ->sum('amount_minor'),
            'currency' => Invoice::value('currency') ?? config('payment.currency', 'TJS'),
        ];
    }

    /** @return array<string, mixed> */
    private function charts(): array
    {
        [$usersChart, $usersLabels] = $this->monthlySeries(User::class, 8);
        [$actLessons, $actLabels] = $this->monthlySeries(Lesson::class, 4);
        [$actTests] = $this->monthlySeries(UserResult::class, 4);

        // Распределение результатов: отлично / средне / слабо.
        $scores = UserResult::selectRaw(
            'sum(score >= 80) as high, sum(score >= 60 and score < 80) as mid, sum(score < 60) as low'
        )->first();

        return [
            'usersChart' => $usersChart,
            'usersLabels' => $usersLabels,
            'actLessons' => $actLessons,
            'actTests' => $actTests,
            'actLabels' => $actLabels,
            'testResultsChart' => [(int) $scores?->high, (int) $scores?->mid, (int) $scores?->low],
        ];
    }

    /**
     * Помесячный ряд за последние $months месяцев.
     *
     * Группируем в базе, а не перебором коллекции: прежний вариант тянул
     * в память все записи за период ради подсчёта по месяцам.
     *
     * @return array{0: array<int, int>, 1: array<int, string>}
     */
    private function monthlySeries(string $model, int $months): array
    {
        $since = now()->subMonths($months)->startOfMonth();

        // Ключ группировки собираем в самом SELECT: pluck умеет брать ключ
        // только из выбранного столбца, выражение туда передать нельзя.
        // Функция форматирования у каждой СУБД своя — боевая база MySQL,
        // тесты идут на SQLite, поэтому выбираем по драйверу.
        $rows = $model::where('created_at', '>=', $since)
            ->selectRaw($this->monthExpression().' as ym, count(*) as n')
            ->groupBy('ym')
            ->pluck('n', 'ym')
            ->all();

        $values = [];
        $labels = [];

        for ($i = $months - 1; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $values[] = (int) ($rows[$date->format('Y-m')] ?? 0);
            $labels[] = self::MONTHS[$date->month - 1];
        }

        return [$values, $labels];
    }

    /** SQL-выражение «год-месяц» для текущего драйвера базы. */
    private function monthExpression(): string
    {
        return match (DB::connection()->getDriverName()) {
            'sqlite' => "strftime('%Y-%m', created_at)",
            'pgsql' => "to_char(created_at, 'YYYY-MM')",
            default => "date_format(created_at, '%Y-%m')",
        };
    }

    /** @return array<string, mixed> */
    private function recentActivity(): array
    {
        return [
            'students' => User::latest()->take(5)->get()->map(fn (User $u) => [
                'name' => $u->name ?? 'Гость',
                'email' => $u->email ?? '—',
                'date' => $u->created_at?->format('d.m.Y') ?? '—',
            ]),

            'lessons' => Lesson::with('level')->latest()->take(5)->get()->map(fn (Lesson $l) => [
                'title' => $l->title,
                'level' => $l->level?->name ?? '—',
                'date' => $l->created_at?->format('d.m.Y') ?? '—',
            ]),

            'progress' => DB::table('user_progress')
                ->join('users', 'users.id', '=', 'user_progress.user_id')
                ->join('lessons', 'lessons.id', '=', 'user_progress.lesson_id')
                ->select('users.name as student', 'lessons.title as lesson', 'user_progress.progress_percent as value')
                ->latest('user_progress.updated_at')
                ->limit(5)
                ->get(),
        ];
    }
}
