@extends('layouts.admin')

@section('title', 'Уроки')

@section('content')
    @php
        $lessons = $lessons ?? collect();
        $levels  = $levels ?? \App\Models\Level::orderBy('name')->get();

        $totalLessons   = \App\Models\Content\Lesson::count();
        $hasStatus      = \Illuminate\Support\Facades\Schema::hasColumn('lessons', 'is_published');
        $publishedCount = $hasStatus ? \App\Models\Content\Lesson::where('is_published', 1)->count() : 0;
        $draftCount     = $totalLessons - $publishedCount;
    @endphp

    <x-admin.page-header title="Уроки">
        <x-slot:sub>
            <span class="live-dot"></span> Всего: <b style="color:var(--text);margin-left:4px">{{ $totalLessons }}</b> уроков
        </x-slot:sub>
        <x-slot:actions>
            <a href="{{ route('admin.content.lessons.create') }}" class="btn btn-primary">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Добавить урок
            </a>
        </x-slot:actions>
    </x-admin.page-header>

    <section class="stats-grid">
        <x-admin.stat-card color="blue" label="Всего уроков" :value="$totalLessons"
            icon='<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>' />

        @if($hasStatus)
            <x-admin.stat-card color="cyan" label="Опубликовано" :value="$publishedCount"
                icon='<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>' />

            <x-admin.stat-card color="deep" label="Черновики" :value="$draftCount"
                icon='<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 4V15l13.5-13.5z"/></svg>' />
        @endif

        <x-admin.stat-card color="yellow" label="Уровней" :value="$levels->count()"
            icon='<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 2 7 12 12 22 7 12 2"/><polyline points="2 17 12 22 22 17"/><polyline points="2 12 12 17 22 12"/></svg>' />
    </section>

    <div class="card">
        <form method="GET" class="filters">
            <input type="text" name="search" class="input" placeholder="Поиск уроков..." value="{{ request('search') }}">
            <select name="level_id" class="select">
                <option value="">Все уровни</option>
                @foreach($levels as $level)
                    <option value="{{ $level->id }}" {{ request('level_id') == $level->id ? 'selected' : '' }}>{{ $level->name }}</option>
                @endforeach
            </select>
            @if($hasStatus)
                <select name="is_published" class="select">
                    <option value="">Все статусы</option>
                    <option value="1" {{ request('is_published') === '1' ? 'selected' : '' }}>Опубликовано</option>
                    <option value="0" {{ request('is_published') === '0' ? 'selected' : '' }}>Черновик</option>
                </select>
            @endif
            <button type="submit" class="btn btn-ghost">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/></svg>
                Фильтр
            </button>
        </form>
    </div>

    <div class="item-grid">
        @forelse($lessons as $lesson)
            @php $published = !$hasStatus || $lesson->is_published; @endphp
            <div class="card item-card">
                <div class="item-top">
                    <div class="item-icon {{ $published ? 'blue' : 'yellow' }}">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>
                    </div>
                    @if($hasStatus)
                        <span class="badge {{ $published ? 'pub' : 'draft' }}">
                            {{ $published ? 'Опубликовано' : 'Черновик' }}
                        </span>
                    @endif
                </div>

                <h3>{{ $lesson->title }}</h3>
                <div class="item-level">Уровень: {{ $lesson->level?->name ?? '—' }}</div>

                <div class="item-meta">
                    <span>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="4" y1="6" x2="20" y2="6"/><line x1="4" y1="12" x2="20" y2="12"/><line x1="4" y1="18" x2="20" y2="18"/></svg>
                        № {{ $lesson->order_number }}
                    </span>
                    <span>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                        {{ $lesson->estimated_minutes }} мин
                    </span>
                </div>

                <div class="item-actions">
                    <a href="{{ route('admin.content.lessons.show', $lesson) }}" class="btn btn-sm btn-ghost">Просмотр</a>
                    <a href="{{ route('admin.content.lessons.edit', $lesson) }}" class="btn btn-sm btn-warning">Изменить</a>
                    <form action="{{ route('admin.content.lessons.destroy', $lesson) }}" method="POST" onsubmit="return confirm('Удалить урок?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger">Удалить</button>
                    </form>
                </div>
            </div>
        @empty
            <x-admin.empty-state message="Уроки не найдены" />
        @endforelse
    </div>

    @if(method_exists($lessons, 'hasPages') && $lessons->hasPages())
        <div class="pagination">
            {{ $lessons->appends(request()->query())->links() }}
        </div>
    @endif
@endsection
