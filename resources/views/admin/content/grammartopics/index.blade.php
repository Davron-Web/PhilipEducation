@extends('layouts.admin')

@section('title', 'Темы грамматики')

@section('content')
    @php
        $topics = $grammarTopics ?? $grammars ?? $topics ?? collect();
        $levels = $levels ?? \App\Models\System\Level::orderBy('name')->get();

        $hasStatus   = \Illuminate\Support\Facades\Schema::hasColumn('grammar_topics', 'is_published');
        $totalTopics = \App\Models\Content\GrammarTopic::count();
    @endphp

    <x-admin.page-header title="Темы грамматики">
        <x-slot:actions>
            <a href="{{ route('admin.content.grammartopics.create') }}" class="btn btn-primary">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Добавить правило
            </a>
        </x-slot:actions>
    </x-admin.page-header>

    <section class="stats-grid">
        <x-admin.stat-card color="blue" label="Всего правил" :value="$totalTopics"
            icon='<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>' />
        <x-admin.stat-card color="yellow" label="Уровней" :value="$levels->count()"
            icon='<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 2 7 12 12 22 7 12 2"/><polyline points="2 17 12 22 22 17"/><polyline points="2 12 12 17 22 12"/></svg>' />
    </section>

    <div class="card">
        <form method="GET" class="filters">
            <input type="text" name="search" class="input" placeholder="Поиск правил..." value="{{ request('search') }}">
            <select name="level_id" class="select">
                <option value="">Все уровни</option>
                @foreach($levels as $level)
                    <option value="{{ $level->id }}" {{ request('level_id') == $level->id ? 'selected' : '' }}>{{ $level->name }}</option>
                @endforeach
            </select>
            <button type="submit" class="btn btn-ghost">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/></svg>
                Фильтр
            </button>
        </form>
    </div>

    <div class="item-grid">
        @forelse($topics as $topic)
            @php $desc = $topic->theory_content ?? $topic->theory ?? $topic->description ?? null; @endphp
            <div class="card item-card">
                <div class="item-top">
                    <div class="item-icon blue">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 4V15l13.5-13.5z"/></svg>
                    </div>
                </div>

                <h3>{{ $topic->title }}</h3>

                @if($desc)
                    <div class="item-level">{{ \Illuminate\Support\Str::limit($desc, 90) }}</div>
                @endif

                <div class="item-level">Уровень: {{ $topic->level?->name ?? '—' }}</div>

                @if($topic->order_number ?? null)
                    <div class="item-meta">
                        <span>
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="4" y1="6" x2="20" y2="6"/><line x1="4" y1="12" x2="20" y2="12"/><line x1="4" y1="18" x2="20" y2="18"/></svg>
                            № {{ $topic->order_number }}
                        </span>
                    </div>
                @endif

                <div class="item-actions">
                    <a href="{{ route('admin.content.grammartopics.show', $topic->id) }}" class="btn btn-sm btn-ghost">Просмотр</a>
                    <a href="{{ route('admin.content.grammartopics.edit', $topic->id) }}" class="btn btn-sm btn-warning">Изменить</a>
                    <form action="{{ route('admin.content.grammartopics.destroy', $topic->id) }}" method="POST" onsubmit="return confirm('Удалить правило?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger">Удалить</button>
                    </form>
                </div>
            </div>
        @empty
            <x-admin.empty-state message="Правил грамматики пока нет" />
        @endforelse
    </div>

    @if(method_exists($topics, 'hasPages') && $topics->hasPages())
        <div class="pagination">
            {{ $topics->appends(request()->query())->links() }}
        </div>
    @endif
@endsection
