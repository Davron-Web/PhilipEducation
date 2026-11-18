@extends('layouts.admin')

@section('title', 'Коллекции слов')

@section('content')
    @php
        $typeLabels = ['level' => 'Уровень', 'topic' => 'Тема', 'exam' => 'Экзамен'];
        $totalCollections = \App\Models\Vocabulary\Collection::count();
        $levelCount = \App\Models\Vocabulary\Collection::where('type', 'level')->count();
        $topicCount = \App\Models\Vocabulary\Collection::where('type', 'topic')->count();
        $examCount = \App\Models\Vocabulary\Collection::where('type', 'exam')->count();
    @endphp

    <x-admin.page-header title="Коллекции слов">
        <x-slot:sub><span class="live-dot"></span> Всего: <b style="color:var(--text);margin-left:4px">{{ $totalCollections }}</b> коллекций</x-slot:sub>
        <x-slot:actions>
            <a href="{{ route('admin.vocabulary.collections.create') }}" class="btn btn-primary">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Добавить коллекцию
            </a>
        </x-slot:actions>
    </x-admin.page-header>

    <section class="stats-grid">
        <x-admin.stat-card color="blue" label="Всего" :value="$totalCollections"
            icon='<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.5"/><rect x="14" y="14" width="7" height="7" rx="1.5"/></svg>' />
        <x-admin.stat-card color="yellow" label="Уровни" :value="$levelCount"
            icon='<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2 2 7l10 5 10-5-10-5Z"/><path d="M2 17l10 5 10-5M2 12l10 5 10-5"/></svg>' />
        <x-admin.stat-card color="cyan" label="Темы" :value="$topicCount"
            icon='<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.59 13.41 13.42 20.6a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82Z"/></svg>' />
        <x-admin.stat-card color="deep" label="Экзамены" :value="$examCount"
            icon='<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>' />
    </section>

    <div class="card">
        <form method="GET" class="filters">
            <input type="text" name="search" class="input" placeholder="Поиск по названию..." value="{{ request('search') }}">
            <select name="type" class="select">
                <option value="">Все типы</option>
                @foreach ($typeLabels as $key => $label)
                    <option value="{{ $key }}" {{ request('type') === $key ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
            <button type="submit" class="btn btn-ghost">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/></svg>
                Фильтр
            </button>
        </form>
    </div>

    <div class="item-grid">
        @forelse ($collections as $collection)
            <div class="card item-card">
                <div class="item-top">
                    <div class="media-thumb-empty">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.5"/><rect x="14" y="14" width="7" height="7" rx="1.5"/></svg>
                    </div>
                    <div style="flex:1;min-width:0">
                        <h3>{{ $collection->title_ru }}</h3>
                        <div class="item-level">{{ $typeLabels[$collection->type] ?? $collection->type }} · {{ $collection->slug }}</div>
                    </div>
                    <span class="badge {{ $collection->is_published ? 'easy' : 'hard' }}">{{ $collection->is_published ? 'Опубликована' : 'Скрыта' }}</span>
                </div>

                <div class="item-meta">
                    <span>{{ $collection->words_count }} слов</span>
                    <span>Порядок: {{ $collection->sort_order }}</span>
                </div>

                <div class="item-actions">
                    <a href="{{ route('admin.vocabulary.collections.show', $collection) }}" class="btn btn-sm btn-ghost">Слова</a>
                    <a href="{{ route('admin.vocabulary.collections.edit', $collection) }}" class="btn btn-sm btn-warning">Изменить</a>
                    <form action="{{ route('admin.vocabulary.collections.destroy', $collection) }}" method="POST" onsubmit="return confirm('Удалить коллекцию «{{ addslashes($collection->title_ru) }}»?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger">Удалить</button>
                    </form>
                </div>
            </div>
        @empty
            <x-admin.empty-state message="Коллекции не найдены" />
        @endforelse
    </div>

    @if ($collections->hasPages())
        <div class="pagination">
            {{ $collections->appends(request()->query())->links() }}
        </div>
    @endif
@endsection
