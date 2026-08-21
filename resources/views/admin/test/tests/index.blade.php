@extends('layouts.admin')

@section('title', 'Тесты')

@section('content')
    @php
        $tests = $tests ?? collect();
        $lessons = $lessons ?? \App\Models\Content\Lesson::orderBy('title')->get();

        $totalTests = \App\Models\Test\Test::count();
        $publishedCount = \App\Models\Test\Test::where('is_published', 1)->count();
        $draftCount = $totalTests - $publishedCount;
        $avgScore = (int) round(\App\Models\Test\Test::avg('passing_score') ?? 0);
    @endphp

    <x-admin.page-header title="Тесты">
        <x-slot:sub><span class="live-dot"></span> Всего: <b style="color:var(--text);margin-left:4px">{{ number_format($totalTests, 0, ',', ' ') }}</b> тестов</x-slot:sub>
        <x-slot:actions>
            <a href="{{ route('admin.test.tests.create') }}" class="btn btn-primary">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Добавить тест
            </a>
        </x-slot:actions>
    </x-admin.page-header>

    <section class="stats-grid">
        <x-admin.stat-card color="blue" label="Всего тестов" :value="$totalTests"
            icon='<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 11 12 14 22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>' />
        <x-admin.stat-card color="green" label="Опубликовано" :value="$publishedCount"
            icon='<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>' />
        <x-admin.stat-card color="yellow" label="Черновики" :value="$draftCount"
            icon='<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 4V15l13.5-13.5z"/></svg>' />
        <x-admin.stat-card color="cyan" :label="'Ср. проходной балл'" :value="$avgScore . '%'"
            icon='<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>' />
    </section>

    <div class="card">
        <form method="GET" class="filters">
            <input type="text" name="search" class="input" placeholder="Поиск по названию..." value="{{ request('search') }}">
            <select name="lesson_id" class="select">
                <option value="">Все уроки</option>
                @foreach($lessons as $lesson)
                    <option value="{{ $lesson->id }}" {{ request('lesson_id') == $lesson->id ? 'selected' : '' }}>{{ $lesson->title }}</option>
                @endforeach
            </select>
            <select name="is_published" class="select">
                <option value="">Все статусы</option>
                <option value="1" {{ request('is_published') === '1' ? 'selected' : '' }}>Опубликовано</option>
                <option value="0" {{ request('is_published') === '0' ? 'selected' : '' }}>Черновик</option>
            </select>
            <button type="submit" class="btn btn-ghost">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/></svg>
                Фильтр
            </button>
        </form>
    </div>

    <div class="card table-card">
        <div class="table-head">
            <h3>Список тестов <small>{{ method_exists($tests, 'count') ? $tests->count() : count($tests) }} на странице</small></h3>
        </div>
        <div class="table-scroll">
            <table class="tbl">
                <thead>
                <tr>
                    <th style="width:70px">ID</th>
                    <th>Урок</th>
                    <th>Название</th>
                    <th style="width:150px">Проходной балл</th>
                    <th style="width:130px">Лимит времени</th>
                    <th style="width:140px">Статус</th>
                    <th style="width:220px;text-align:right">Действия</th>
                </tr>
                </thead>
                <tbody>
                @forelse($tests as $test)
                    @php
                        $score = (int) ($test->passing_score ?? 0);
                        $scoreClass = $score >= 80 ? 'score-high' : ($score >= 60 ? 'score-mid' : 'score-low');
                        $isPub = (bool) ($test->is_published ?? false);
                    @endphp
                    <tr>
                        <td class="id-cell">#{{ $test->id }}</td>
                        <td>
                            @if($test->lesson)
                                <a class="chip-link" href="{{ route('admin.content.lessons.show', $test->lesson) }}">
                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>
                                    {{ \Illuminate\Support\Str::limit($test->lesson->title ?? 'Урок', 30) }}
                                </a>
                            @else
                                <span class="id-cell">—</span>
                            @endif
                        </td>
                        <td class="title-cell">{{ $test->title }}</td>
                        <td><span class="score-badge {{ $scoreClass }}"><span class="score-dot"></span>{{ $score }}%</span></td>
                        <td>
                            @if($test->time_limit)
                                <span class="id-cell">{{ $test->time_limit }} мин</span>
                            @else
                                <span class="id-cell">—</span>
                            @endif
                        </td>
                        <td>
                            <span class="status-pill {{ $isPub ? 'status-published' : 'status-draft' }}">
                                <span class="status-dot"></span>
                                {{ $isPub ? 'Опубликовано' : 'Черновик' }}
                            </span>
                        </td>
                        <td>
                            <div class="actions">
                                <a href="{{ route('admin.test.tests.show', $test) }}" class="btn btn-sm btn-ghost">Просмотр</a>
                                <a href="{{ route('admin.test.tests.edit', $test) }}" class="btn btn-sm btn-warning">Изменить</a>
                                <form action="{{ route('admin.test.tests.destroy', $test) }}" method="POST" onsubmit="return confirm('Удалить тест «{{ addslashes($test->title ?? '') }}»?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">Удалить</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr class="empty-row">
                        <td colspan="7">
                            <div class="empty-icon">
                                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 11 12 14 22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
                            </div>
                            <b>Тесты не найдены</b>
                            <p style="color:var(--muted);margin-top:6px;font-size:13px">Добавьте первый тест или измените фильтры</p>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if(method_exists($tests, 'hasPages') && $tests->hasPages())
        <div class="pagination">{{ $tests->withQueryString()->links() }}</div>
    @endif
@endsection
