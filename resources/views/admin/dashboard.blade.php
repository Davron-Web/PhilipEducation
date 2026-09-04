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

    <x-admin.page-header title="Панель управления" />

    <section class="ph-admin-hero">
        <div class="ph-aurora ph-aurora-a"></div>
        <div class="ph-aurora ph-aurora-b"></div>
        <canvas class="ph-snow" data-ph-snow></canvas>
        <div class="ph-admin-hero-content">
            <div class="ph-admin-hero-badge">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="#FFD600" stroke="none"><path d="M12 2l2.9 6.6L22 9.3l-5 4.9 1.2 7.1L12 17.8l-6.2 3.5L7 14.2 2 9.3l7.1-.7L12 2z"/></svg>
                Администратор
            </div>
            <h2>С возвращением, {{ explode(' ', auth()->user()->name ?? 'Admin')[0] }}!</h2>
            <p>На платформе {{ number_format($usersCount, 0, ',', ' ') }} учеников. Проверьте свежую активность или создайте новый урок.</p>
            <div class="ph-admin-hero-actions">
                <a href="{{ route('admin.content.lessons.create') }}" class="btn btn-primary">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                    Новый урок
                </a>
                <a href="{{ route('admin.ai') }}" class="btn btn-ghost ph-admin-hero-ghost">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2a4 4 0 0 1 4 4v1a2 2 0 0 1 2 2v1a2 2 0 0 0 2 2 2 2 0 0 1 0 4 2 2 0 0 0-2 2v1a2 2 0 0 1-2 2v1a4 4 0 0 1-8 0v-1a2 2 0 0 1-2-2v-1a2 2 0 0 0-2-2 2 2 0 0 1 0-4 2 2 0 0 0 2-2V9a2 2 0 0 1 2-2V6a4 4 0 0 1 4-4z"/><circle cx="12" cy="12" r="2"/></svg>
                    AI-генерация
                </a>
            </div>
        </div>
        <div class="ph-admin-hero-owl">
            <svg width="72" height="72" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"><path d="M6 6l2 3"/><path d="M18 6l-2 3"/><ellipse cx="12" cy="13" rx="7" ry="8"/><circle cx="9" cy="12" r="2.2"/><circle cx="15" cy="12" r="2.2"/><circle cx="9" cy="12" r=".4" fill="#fff" stroke="none"/><circle cx="15" cy="12" r=".4" fill="#fff" stroke="none"/><path d="M11.3 14.5h1.4l-.7 1.2z" fill="#fff" stroke="none"/></svg>
        </div>
    </section>

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

    <section class="stats-grid">
        <x-admin.stat-card color="blue" label="Новых за 30 дней" :value="number_format($newUsers, 0, ',', ' ')"
            icon='<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><line x1="19" y1="8" x2="19" y2="14"/><line x1="22" y1="11" x2="16" y2="11"/></svg>' />
        <x-admin.stat-card color="blue" label="Активных за 30 дней" :value="number_format($activeUsers, 0, ',', ' ')"
            icon='<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 12h-4l-3 9L9 3l-3 9H2"/></svg>' />
        <x-admin.stat-card color="yellow" label="PRO / FREE" :value="number_format($proUsers, 0, ',', ' ') . ' / ' . number_format($freeUsers, 0, ',', ' ')"
            icon='<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2l2.9 6.6L22 9.3l-5 4.9 1.2 7.1L12 17.8l-6.2 3.5L7 14.2 2 9.3l7.1-.7L12 2z"/></svg>' />
        <x-admin.stat-card color="yellow" label="Доход за месяц" :value="number_format($revenueMonth / 100, 0, ',', ' ') . ' ' . $currency"
            icon='<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>' />
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
