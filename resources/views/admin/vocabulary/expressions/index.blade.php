@extends('layouts.admin')

@section('title', 'Выражения')

@section('content')
    @php
        $expressions = $expressions ?? collect();
        $levels = $levels ?? [];

        $types = [
            'idiom' => 'Идиома',
            'phrasal_verb' => 'Фразовый глагол',
            'proverb' => 'Пословица',
            'collocation' => 'Коллокация',
        ];

        $totalExpressions = \App\Models\Vocabulary\Expression::count();
        $idiomCount = \App\Models\Vocabulary\Expression::where('type', 'idiom')->count();
        $phrasalCount = \App\Models\Vocabulary\Expression::where('type', 'phrasal_verb')->count();
        $hasTranslation = \Illuminate\Support\Facades\Schema::hasTable('expression_translations');
    @endphp

    <x-admin.page-header title="Выражения">
        <x-slot:sub><span class="live-dot"></span> Всего: <b style="color:var(--text);margin-left:4px">{{ $totalExpressions }}</b> выражений</x-slot:sub>
        <x-slot:actions>
            <a href="{{ route('admin.vocabulary.expressions.create') }}" class="btn btn-primary">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Добавить выражение
            </a>
        </x-slot:actions>
    </x-admin.page-header>

    <section class="stats-grid">
        <x-admin.stat-card color="blue" label="Всего выражений" :value="$totalExpressions"
            icon='<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>' />
        <x-admin.stat-card color="yellow" label="Идиомы" :value="$idiomCount"
            icon='<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"/></svg>' />
        <x-admin.stat-card color="cyan" label="Фразовые глаголы" :value="$phrasalCount"
            icon='<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="5 12 19 12"/><polyline points="12 5 19 12 12 19"/></svg>' />
        <x-admin.stat-card color="deep" :label="'Переводы'" :value="$hasTranslation ? '✓' : '—'"
            icon='<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>' />
    </section>

    <div class="card">
        <form method="GET" class="filters">
            <input type="text" name="search" class="input" placeholder="Поиск выражения..." value="{{ request('search') }}">
            <select name="type" class="select">
                <option value="">Все типы</option>
                @foreach($types as $key => $label)
                    <option value="{{ $key }}" {{ request('type') === $key ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
            <select name="level_id" class="select">
                <option value="">Любой уровень</option>
                @foreach($levels as $id => $code)
                    <option value="{{ $id }}" {{ request('level_id') == $id ? 'selected' : '' }}>{{ $code }}</option>
                @endforeach
            </select>
            <button type="submit" class="btn btn-ghost">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/></svg>
                Фильтр
            </button>
        </form>
    </div>

    <div class="item-grid">
        @forelse($expressions as $expression)
            @php
                $diff = (int) ($expression->difficulty ?? 0);
                $diffClass = $diff <= 2 ? 'easy' : ($diff == 3 ? 'medium' : 'hard');
                $diffLabel = $diff <= 2 ? 'Легко' : ($diff == 3 ? 'Средне' : 'Сложно');
                $translation = $expression->translations && $expression->translations->count() ? $expression->translations->first()->translation : '';
            @endphp
            <div class="card item-card">
                <div class="item-top">
                    <div class="media-thumb-empty">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                    </div>
                    <div style="flex:1;min-width:0">
                        <h3>{{ $expression->text }} <x-speak-button :word="$expression->text" :audio-url="$expression->audio_url" /></h3>
                        <div class="item-level">{{ $types[$expression->type] ?? $expression->type }}{{ $expression->level ? ' · '.$expression->level->code : '' }}</div>
                        @if($translation)
                            <div class="item-level">{{ $translation }}</div>
                        @endif
                    </div>
                    <span class="badge {{ $diffClass }}">{{ $diffLabel }}</span>
                </div>

                @if($expression->category)
                    <div class="chip-link" style="cursor:default">{{ $expression->category }}</div>
                @endif

                <div class="item-meta">
                    <span>ID {{ $expression->id }}</span>
                    <span>Сложность {{ $diff }}/5</span>
                    <span>{{ optional($expression->created_at)->format('d.m.Y') ?? '—' }}</span>
                </div>

                <div class="item-actions">
                    <a href="{{ route('admin.vocabulary.expressions.show', $expression) }}" class="btn btn-sm btn-ghost">Просмотр</a>
                    <a href="{{ route('admin.vocabulary.expressions.edit', $expression) }}" class="btn btn-sm btn-warning">Изменить</a>
                    <form action="{{ route('admin.vocabulary.expressions.destroy', $expression) }}" method="POST" onsubmit="return confirm('Удалить выражение «{{ addslashes($expression->text) }}»?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger">Удалить</button>
                    </form>
                </div>
            </div>
        @empty
            <x-admin.empty-state message="Выражения не найдены" />
        @endforelse
    </div>

    @if(method_exists($expressions, 'hasPages') && $expressions->hasPages())
        <div class="pagination">
            {{ $expressions->appends(request()->query())->links() }}
        </div>
    @endif
@endsection
