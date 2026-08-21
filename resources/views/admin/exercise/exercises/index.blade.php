@extends('layouts.admin')

@section('title', 'Упражнения')

@section('content')
    @php
        $exercises = $exercises ?? collect();
        $types     = $types ?? [];

        $rawLessons = $lessons ?? null;
        if ($rawLessons === null && class_exists(\App\Models\Content\Lesson::class)) {
            try { $rawLessons = \App\Models\Content\Lesson::orderBy('title')->pluck('title', 'id')->toArray(); }
            catch (\Throwable $e) { $rawLessons = []; }
        }
        $lessons = [];
        if (is_iterable($rawLessons)) {
            foreach ($rawLessons as $key => $item) {
                if (is_object($item)) {
                    $id = $item->id ?? $key;
                    $lessons[$id] = $item->title ?? $item->name ?? ('Урок #' . $id);
                } elseif (is_array($item)) {
                    $id = $item['id'] ?? $key;
                    $lessons[$id] = $item['title'] ?? $item['name'] ?? ('Урок #' . $id);
                } else {
                    $lessons[$key] = (string) $item;
                }
            }
        }

        $exModel = class_exists(\App\Models\Exercise\Exercise::class) ? \App\Models\Exercise\Exercise::class : (class_exists(\App\Models\Exercise::class) ? \App\Models\Exercise::class : null);
        $totalExercises = $exModel ? $exModel::count() : (method_exists($exercises, 'total') ? $exercises->total() : $exercises->count());

        $withLesson = 0; $withoutLesson = 0;
        try {
            if ($exModel && \Illuminate\Support\Facades\Schema::hasColumn((new $exModel)->getTable(), 'lesson_id')) {
                $withLesson    = (int) $exModel::whereNotNull('lesson_id')->count();
                $withoutLesson = $totalExercises - $withLesson;
            }
        } catch (\Throwable $e) {}

        $typesCount = count($types);
    @endphp

    <x-admin.page-header title="Упражнения">
        <x-slot:sub>
            <span class="live-dot"></span> Всего: <b style="color:var(--text);margin-left:4px">{{ number_format($totalExercises, 0, ',', ' ') }}</b> упражнений
        </x-slot:sub>
        <x-slot:actions>
            <a href="{{ route('admin.exercise.exercises.create') }}" class="btn btn-primary">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Добавить упражнение
            </a>
        </x-slot:actions>
    </x-admin.page-header>

    <section class="stats-grid">
        <x-admin.stat-card color="blue" label="Всего упражнений" :value="$totalExercises"
            icon='<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"/></svg>' />
        <x-admin.stat-card color="cyan" label="Привязано к урокам" :value="$withLesson"
            icon='<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>' />
        <x-admin.stat-card color="yellow" label="Типов упражнений" :value="$typesCount"
            icon='<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>' />
        <x-admin.stat-card color="deep" label="Без урока" :value="$withoutLesson"
            icon='<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>' />
    </section>

    <div class="card">
        <form method="GET" class="filters">
            <input type="text" name="search" class="input" placeholder="Поиск по названию..." value="{{ request('search') }}">
            <select name="lesson_id" class="select">
                <option value="">Все уроки</option>
                @foreach($lessons as $id => $title)
                    <option value="{{ $id }}" {{ request('lesson_id') == $id ? 'selected' : '' }}>{{ $title }}</option>
                @endforeach
            </select>
            <select name="type" class="select">
                <option value="">Все типы</option>
                @foreach($types as $type => $label)
                    <option value="{{ $type }}" {{ request('type') == $type ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
            <button type="submit" class="btn btn-ghost">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/></svg>
                Фильтр
            </button>
            <a href="{{ route('admin.exercise.exercises.index') }}" class="btn btn-ghost">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 2.13-9.36L1 10"/></svg>
                Сброс
            </a>
        </form>
    </div>

    <div class="card table-card">
        <div class="table-head">
            <h3>Список упражнений <small>{{ method_exists($exercises, 'count') ? $exercises->count() : count($exercises) }} на странице</small></h3>
        </div>
        <div class="table-scroll">
            <table class="tbl">
                <thead>
                <tr>
                    <th style="width:80px">ID</th>
                    <th>Урок</th>
                    <th>Название</th>
                    <th style="width:150px">Тип</th>
                    <th>Инструкции</th>
                    <th style="width:220px;text-align:right">Действия</th>
                </tr>
                </thead>
                <tbody>
                @forelse($exercises as $exercise)
                    @php
                        $exId    = $exercise->id ?? '';
                        $exTitle = $exercise->title ?? 'Без названия';
                        $exLesson = $exercise->lesson ?? null;
                        $exInstr = \Illuminate\Support\Str::limit(strip_tags((string) ($exercise->instructions ?? '')), 80);
                        $exLabel = $exercise->type_label ?? ($exercise->type ?? '—');
                        $exColor = $exercise->type_badge_color ?? 'secondary';
                        $allowedColors = ['primary', 'secondary', 'success', 'info', 'warning', 'danger', 'light', 'dark'];
                        if (!in_array($exColor, $allowedColors, true)) $exColor = 'secondary';
                    @endphp
                    <tr>
                        <td class="id-cell">#{{ $exId }}</td>
                        <td>
                            @if($exLesson)
                                <a class="chip-link" href="{{ route('admin.content.lessons.show', $exLesson) }}">
                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>
                                    {{ $exLesson->title ?? 'Урок' }}
                                </a>
                            @else
                                <span class="id-cell">—</span>
                            @endif
                        </td>
                        <td class="title-cell">{{ $exTitle }}</td>
                        <td><span class="badge badge-{{ $exColor }}">{{ $exLabel }}</span></td>
                        <td class="text-cell">{{ $exInstr ?: '—' }}</td>
                        <td>
                            <div class="actions">
                                <a href="{{ route('admin.exercise.exercises.show', $exercise) }}" class="btn btn-sm btn-ghost">Просмотр</a>
                                <a href="{{ route('admin.exercise.exercises.edit', $exercise) }}" class="btn btn-sm btn-warning">Изменить</a>
                                <form action="{{ route('admin.exercise.exercises.destroy', $exercise) }}" method="POST" onsubmit="return confirm('Удалить упражнение «{{ addslashes($exTitle) }}»?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">Удалить</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr class="empty-row">
                        <td colspan="6">
                            <div class="empty-icon">
                                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"/></svg>
                            </div>
                            <b>Упражнения не найдены</b>
                            <p style="color:var(--muted);margin-top:6px;font-size:13px">Добавьте первое упражнение или измените фильтры</p>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if(method_exists($exercises, 'hasPages') && $exercises->hasPages())
        <div class="pagination">
            {{ $exercises->withQueryString()->links() }}
        </div>
    @endif
@endsection
