@extends('layouts.admin')

@section('title', 'Панель управления')

@push('styles')
<style>
    .charts-grid { display: grid; grid-template-columns: 1.4fr 1fr 1fr; gap: 20px; }
    .chart-box { position: relative; height: 240px; }
    .tables-grid { display: grid; grid-template-columns: 1.25fr 1fr 1.15fr; gap: 20px; }
    .dash-table { width: 100%; border-collapse: collapse; }
    .dash-table th { text-align: left; font-size: 11px; text-transform: uppercase; letter-spacing: .05em; color: var(--muted); padding: 6px 10px; }
    .dash-table td { padding: 10px; border-top: 1px solid var(--border); font-size: 13.5px; vertical-align: middle; }
    .cell-user { display: flex; align-items: center; gap: 10px; font-weight: 600; }
    .avatar-sm { width: 30px; height: 30px; font-size: 11px; }
    .dash-empty-row td { text-align: center; color: var(--muted); padding: 24px; font-style: italic; }
    .progress-cell { display: flex; align-items: center; gap: 10px; }
    .progress-cell .progress { flex: 1; height: 8px; }
    .progress-val { width: 40px; text-align: right; font-weight: 700; font-size: 12.5px; }
    @media (max-width: 1200px) { .charts-grid { grid-template-columns: 1fr 1fr; } .charts-grid .card:first-child { grid-column: 1/-1; } .tables-grid { grid-template-columns: 1fr; } }
</style>
@endpush

@section('content')
    @php
        $safeCount = function (string $model): int {
            if (! class_exists($model)) return 0;
            try { return (int) $model::count(); } catch (\Throwable) { return 0; }
        };
        $months = ['Янв','Фев','Мар','Апр','Май','Июн','Июл','Авг','Сен','Окт','Ноя','Дек'];

        $usersCount = $safeCount(\App\Models\User::class);
        $wordsCount = $safeCount(\App\Models\Vocabulary\Word::class);
        $lessonsDone = (int) \App\Models\User\UserProgress::where('is_completed', true)->count();
        $avgScore = (int) round(\App\Models\User\UserResult::avg('score') ?? 0);

        $countByMonth = function (string $model, \Illuminate\Support\Carbon $since): array {
            return $model::where('created_at', '>=', $since)
                ->pluck('created_at')
                ->countBy(fn ($date) => $date->month)
                ->toArray();
        };

        $usersChart = array_fill(0, 8, 0);
        $rows = $countByMonth(\App\Models\User::class, now()->subMonths(8));
        for ($i = 0; $i < 8; $i++) {
            $m = now()->subMonths(7 - $i)->month;
            $usersChart[$i] = (int) ($rows[$m] ?? 0);
        }
        $usersLabels = [];
        for ($i = 0; $i < 8; $i++) $usersLabels[] = $months[now()->subMonths(7 - $i)->month - 1];

        $actLessons = array_fill(0, 4, 0);
        $actTests = array_fill(0, 4, 0);
        $l = $countByMonth(\App\Models\Content\Lesson::class, now()->subMonths(4));
        $t = $countByMonth(\App\Models\User\UserResult::class, now()->subMonths(4));
        for ($i = 0; $i < 4; $i++) {
            $m = now()->subMonths(3 - $i)->month;
            $actLessons[$i] = (int) ($l[$m] ?? 0);
            $actTests[$i] = (int) ($t[$m] ?? 0);
        }
        $actLabels = [];
        for ($i = 0; $i < 4; $i++) $actLabels[] = $months[now()->subMonths(3 - $i)->month - 1];

        $testResultsChart = [0, 0, 0];
        foreach (\App\Models\User\UserResult::all() as $r) {
            $s = (int) ($r->score ?? 0);
            if ($s >= 80) $testResultsChart[0]++;
            elseif ($s >= 60) $testResultsChart[1]++;
            else $testResultsChart[2]++;
        }

        $students = \App\Models\User::latest()->take(5)->get()->map(fn ($u) => [
            'name' => $u->name ?? 'Гость', 'email' => $u->email ?? '—', 'date' => optional($u->created_at)->format('d.m.Y') ?? '—',
        ]);

        $lessons = \App\Models\Content\Lesson::with('level')->latest()->take(5)->get()->map(fn ($l) => [
            'title' => $l->title, 'level' => optional($l->level)->name ?? '—', 'date' => optional($l->created_at)->format('d.m.Y') ?? '—',
        ]);

        $progress = \Illuminate\Support\Facades\DB::table('user_progress')
            ->join('users', 'users.id', '=', 'user_progress.user_id')
            ->join('lessons', 'lessons.id', '=', 'user_progress.lesson_id')
            ->select('users.name as student', 'lessons.title as lesson', 'user_progress.progress_percent as value')
            ->latest('user_progress.updated_at')->limit(5)->get();
    @endphp

    <x-admin.page-header title="Панель управления" />

    <section class="stats-grid">
        <x-admin.stat-card color="blue" label="Всего учеников" :value="number_format($usersCount, 0, ',', ' ')"
            icon='<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>' />
        <x-admin.stat-card color="yellow" label="Слов в словаре" :value="number_format($wordsCount, 0, ',', ' ')"
            icon='<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>' />
        <x-admin.stat-card color="blue" label="Завершённые уроки" :value="number_format($lessonsDone, 0, ',', ' ')"
            icon='<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>' />
        <x-admin.stat-card color="yellow" :label="'Средний результат тестов'" :value="$avgScore . '%'"
            icon='<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="6"/><polyline points="8.5 13.5 7 22 12 19 17 22 15.5 13.5"/></svg>' />
    </section>

    <section class="charts-grid">
        <div class="card"><h3>Рост пользователей</h3><div class="chart-box"><canvas id="chartUsers"></canvas></div></div>
        <div class="card"><h3>Активность обучения</h3><div class="chart-box"><canvas id="chartActivity"></canvas></div></div>
        <div class="card"><h3>Результаты тестов</h3><div class="chart-box"><canvas id="chartTests"></canvas></div></div>
    </section>

    <section class="tables-grid">
        <div class="card">
            <h3>Последние зарегистрированные ученики</h3>
            <table class="dash-table">
                <thead><tr><th>Имя</th><th>Email</th><th>Дата</th></tr></thead>
                <tbody>
                @forelse($students as $s)
                    <tr>
                        <td>
                            <div class="cell-user">
                                <span class="avatar-circle avatar-sm">{{ strtoupper(mb_substr($s['name'], 0, 1)) }}</span>
                                {{ $s['name'] }}
                            </div>
                        </td>
                        <td style="color:var(--muted)">{{ $s['email'] }}</td>
                        <td>{{ $s['date'] }}</td>
                    </tr>
                @empty
                    <tr class="dash-empty-row"><td colspan="3">Пока нет зарегистрированных учеников</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div class="card">
            <h3>Последние уроки</h3>
            <table class="dash-table">
                <thead><tr><th>Урок</th><th>Уровень</th><th>Дата</th></tr></thead>
                <tbody>
                @forelse($lessons as $l)
                    <tr><td><b>{{ $l['title'] }}</b></td><td style="color:var(--muted)">{{ $l['level'] }}</td><td>{{ $l['date'] }}</td></tr>
                @empty
                    <tr class="dash-empty-row"><td colspan="3">Пока нет уроков</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div class="card">
            <h3>Прогресс обучения</h3>
            <table class="dash-table">
                <thead><tr><th>Ученик</th><th>Урок</th><th style="width:45%">Прогресс</th></tr></thead>
                <tbody>
                @forelse($progress as $p)
                    <tr>
                        <td><b>{{ $p->student }}</b></td>
                        <td style="color:var(--muted)">{{ $p->lesson }}</td>
                        <td>
                            <div class="progress-cell">
                                <div class="progress"><div class="progress-bar" style="width: {{ min(100, max(0, $p->value)) }}%"></div></div>
                                <span class="progress-val">{{ $p->value }}%</span>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr class="dash-empty-row"><td colspan="3">Ученики ещё не приступили к занятиям</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </section>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
    const rootEl = document.documentElement;
    const css = (n) => getComputedStyle(rootEl).getPropertyValue(n).trim();
    const BLUE = '#0047FF', YELLOW = '#FFD600', LIGHT_BLUE = '#93C5FD';
    const charts = [];
    const usersLabels = @json($usersLabels), usersData = @json($usersChart), actLabels = @json($actLabels), actLessons = @json($actLessons), actTests = @json($actTests), testsData = @json($testResultsChart);
    const allUsersZero = usersData.every(v => v === 0), allTestsZero = testsData.every(v => v === 0);

    const ctx1 = document.getElementById('chartUsers').getContext('2d');
    const grad1 = ctx1.createLinearGradient(0, 0, 0, 240);
    grad1.addColorStop(0, 'rgba(0,71,255,.28)'); grad1.addColorStop(1, 'rgba(0,71,255,0)');
    charts.push(new Chart(ctx1, { type: 'line', data: { labels: usersLabels, datasets: [{ data: usersData, borderColor: BLUE, backgroundColor: grad1, fill: true, tension: .4, borderWidth: 3, pointRadius: 4, pointBackgroundColor: BLUE }] }, options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false }, tooltip: { enabled: !allUsersZero } }, scales: { x: { grid: { display: false } }, y: { beginAtZero: true, ticks: { precision: 0 } } } } }));

    charts.push(new Chart(document.getElementById('chartActivity'), { type: 'bar', data: { labels: actLabels, datasets: [{ label: 'Уроки', data: actLessons, backgroundColor: BLUE, borderRadius: 6 }, { label: 'Тесты', data: actTests, backgroundColor: YELLOW, borderRadius: 6 }] }, options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom', labels: { boxWidth: 10, boxHeight: 10 } } }, scales: { x: { grid: { display: false } }, y: { beginAtZero: true, ticks: { precision: 0 } } } } }));

    charts.push(new Chart(document.getElementById('chartTests'), { type: 'doughnut', data: { labels: ['Отлично (≥80%)', 'Хорошо (≥60%)', 'Нужна работа (<60%)'], datasets: [{ data: allTestsZero ? [1, 1, 1] : testsData, backgroundColor: allTestsZero ? ['#CBD5E1', '#E2E8F0', '#F1F5F9'] : [BLUE, YELLOW, LIGHT_BLUE], borderWidth: 0 }] }, options: { responsive: true, maintainAspectRatio: false, cutout: '68%', plugins: { legend: { position: 'bottom', labels: { boxWidth: 10, boxHeight: 10, padding: 8 } }, tooltip: { enabled: !allTestsZero } } } }));

    function refreshChartsTheme() {
        const muted = css('--muted'), border = css('--border'), text = css('--text');
        charts.forEach(ch => {
            if (ch.options.scales) { Object.values(ch.options.scales).forEach(s => { if (s.ticks) s.ticks.color = muted; if (s.grid) s.grid.color = border; }); }
            if (ch.options.plugins.legend) ch.options.plugins.legend.labels.color = text;
            ch.update();
        });
    }
    refreshChartsTheme();

    const themeToggle = document.getElementById('themeToggle');
    if (themeToggle) themeToggle.addEventListener('change', refreshChartsTheme);
</script>
@endpush
