@extends('layouts.admin')

@section('title', 'Словарь')

@section('content')
    @php
        $words = $words ?? collect();

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

        $totalWords     = \App\Models\Vocabulary\Word::count();
        $withImageCount = \App\Models\Vocabulary\Word::whereNotNull('image')->where('image', '!=', '')->count();
        $hasTranslation = \Illuminate\Support\Facades\Schema::hasTable('word_translations');
        $lessonCount    = \App\Models\Content\Lesson::count();
    @endphp

    <x-admin.page-header title="Словарь">
        <x-slot:sub><span class="live-dot"></span> Всего: <b style="color:var(--text);margin-left:4px">{{ $totalWords }}</b> слов</x-slot:sub>
        <x-slot:actions>
            <a href="{{ route('admin.vocabulary.words.create') }}" class="btn btn-primary">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Добавить слово
            </a>
        </x-slot:actions>
    </x-admin.page-header>

    <section class="stats-grid">
        <x-admin.stat-card color="blue" label="Всего слов" :value="$totalWords"
            icon='<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>' />
        <x-admin.stat-card color="yellow" label="С картинками" :value="$withImageCount"
            icon='<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>' />
        <x-admin.stat-card color="cyan" :label="'Переводы'" :value="$hasTranslation ? '✓' : '—'"
            icon='<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>' />
        <x-admin.stat-card color="deep" label="Уроков в базе" :value="$lessonCount"
            icon='<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 2 7 12 12 22 7 12 2"/><polyline points="2 17 12 22 22 17"/><polyline points="2 12 12 17 22 12"/></svg>' />
    </section>

    <div class="card">
        <form method="GET" class="filters">
            <input type="text" name="search" class="input" placeholder="Поиск слова..." value="{{ request('search') }}">
            <select name="lesson_id" class="select">
                <option value="">Все уроки</option>
                @foreach($lessons as $id => $title)
                    <option value="{{ $id }}" {{ request('lesson_id') == $id ? 'selected' : '' }}>{{ $title }}</option>
                @endforeach
            </select>
            <select name="difficulty" class="select">
                <option value="">Любая сложность</option>
                <option value="easy" {{ request('difficulty') === 'easy' ? 'selected' : '' }}>Легко (1–2)</option>
                <option value="medium" {{ request('difficulty') === 'medium' ? 'selected' : '' }}>Средне (3)</option>
                <option value="hard" {{ request('difficulty') === 'hard' ? 'selected' : '' }}>Сложно (4–5)</option>
            </select>
            <button type="submit" class="btn btn-ghost">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/></svg>
                Фильтр
            </button>
        </form>
    </div>

    <div class="item-grid">
        @forelse($words as $word)
            @php
                $diff = (int) ($word->difficulty ?? 0);
                $diffClass = $diff <= 2 ? 'easy' : ($diff == 3 ? 'medium' : 'hard');
                $diffLabel = $diff <= 2 ? 'Легко' : ($diff == 3 ? 'Средне' : 'Сложно');
                $translation = $word->translations && $word->translations->count() ? $word->translations->first()->translation : '';
            @endphp
            <div class="card item-card">
                <div class="item-top">
                    @if($word->image)
                        <img src="{{ $word->image }}" alt="{{ $word->word }}" class="media-thumb">
                    @else
                        <div class="media-thumb-empty">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                        </div>
                    @endif
                    <div style="flex:1;min-width:0">
                        <h3>{{ $word->word }} <x-speak-button :word="$word->word" :audio-url="$word->audio_url" /></h3>
                        @if($word->transcription)
                            <div class="item-level">[{{ $word->transcription }}]</div>
                        @endif
                        @if($translation)
                            <div class="item-level">{{ $translation }}</div>
                        @endif
                    </div>
                    <span class="badge {{ $diffClass }}">{{ $diffLabel }}</span>
                </div>

                @if($word->lesson)
                    <a href="{{ route('admin.content.lessons.show', $word->lesson) }}" class="chip-link">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="14" height="14"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>
                        {{ $word->lesson->title }}
                    </a>
                @endif

                @php $dupCount = $duplicateCounts[mb_strtolower($word->word)] ?? 1; @endphp
                @if($dupCount > 1)
                    <div class="item-level" style="margin-top:6px;color:var(--muted)" title="Это слово специально повторяется в нескольких уроках со своим примером под грамматику каждого урока — не дубль-баг.">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="12" height="12" style="vertical-align:-1px"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                        также в {{ $dupCount - 1 }} {{ $dupCount - 1 == 1 ? 'другом уроке' : 'других уроках' }}
                    </div>
                @endif

                <div class="item-meta">
                    <span>ID {{ $word->id }}</span>
                    <span>Сложность {{ $diff }}/5</span>
                    <span>{{ optional($word->created_at)->format('d.m.Y') ?? '—' }}</span>
                </div>

                <div class="item-actions">
                    <a href="{{ route('admin.vocabulary.words.show', $word) }}" class="btn btn-sm btn-ghost">Просмотр</a>
                    <a href="{{ route('admin.vocabulary.words.edit', $word) }}" class="btn btn-sm btn-warning">Изменить</a>
                    <form action="{{ route('admin.vocabulary.words.destroy', $word) }}" method="POST" onsubmit="return confirm('Удалить слово «{{ addslashes($word->word) }}»?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger">Удалить</button>
                    </form>
                </div>
            </div>
        @empty
            <x-admin.empty-state message="Слова не найдены" />
        @endforelse
    </div>

    @if(method_exists($words, 'hasPages') && $words->hasPages())
        <div class="pagination">
            {{ $words->appends(request()->query())->links() }}
        </div>
    @endif
@endsection
