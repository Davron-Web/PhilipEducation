@extends('layouts.admin')

@section('title', $lesson->title)

@section('content')
    @php
        $hasStatus = \Illuminate\Support\Facades\Schema::hasColumn('lessons', 'is_published');
        $published = !$hasStatus || $lesson->is_published;
    @endphp

    <x-admin.page-header :title="$lesson->title" :backRoute="route('admin.content.lessons.index')">
        @if($hasStatus)
            <x-slot:badge>
                <span class="badge {{ $published ? 'pub' : 'draft' }}">{{ $published ? 'Опубликовано' : 'Черновик' }}</span>
            </x-slot:badge>
        @endif
        <x-slot:actions>
            <a href="{{ route('admin.content.lessons.edit', $lesson) }}" class="btn btn-warning">Изменить</a>
            <form action="{{ route('admin.content.lessons.destroy', $lesson) }}" method="POST" onsubmit="return confirm('Удалить урок?')" style="margin:0">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">Удалить</button>
            </form>
        </x-slot:actions>
    </x-admin.page-header>

    <section class="stats-grid">
        <x-admin.stat-card color="yellow" label="Уровень" :value="$lesson->level?->name ?? '—'"
            icon='<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 2 7 12 12 22 7 12 2"/><polyline points="2 17 12 22 22 17"/><polyline points="2 12 12 17 22 12"/></svg>' />

        <x-admin.stat-card color="blue" label="Порядок" :value="'№ ' . $lesson->order_number"
            icon='<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="4" y1="6" x2="20" y2="6"/><line x1="4" y1="12" x2="20" y2="12"/><line x1="4" y1="18" x2="20" y2="18"/></svg>' />

        <x-admin.stat-card color="yellow" label="Длительность" :value="$lesson->estimated_minutes . ' мин'"
            icon='<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>' />

        <x-admin.stat-card color="blue" :label="'Статус'" :value="$published ? 'Опубликовано' : 'Черновик'"
            icon='<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>' />
    </section>

    <div class="card">
        <h3>Информация</h3>
        <div class="info-row"><span class="info-label">ID</span><span class="info-value">#{{ $lesson->id }}</span></div>
        <div class="info-row"><span class="info-label">Описание</span><span class="info-value">{{ $lesson->description ?: '—' }}</span></div>
        <div class="info-row"><span class="info-label">Создан</span><span class="info-value">{{ $lesson->created_at?->format('d.m.Y H:i') }}</span></div>
        <div class="info-row"><span class="info-label">Обновлён</span><span class="info-value">{{ $lesson->updated_at?->format('d.m.Y H:i') }}</span></div>
    </div>

    <div class="card table-card">
        <div class="table-head">
            <h3>Содержание урока <small>{{ $lesson->contents->count() }}</small></h3>
            <a href="{{ route('admin.content.lessoncontents.create') }}?lesson_id={{ $lesson->id }}" class="btn btn-sm btn-primary">Добавить блок</a>
        </div>
        @forelse($lesson->contents->sortBy('order_number') as $block)
            <div class="richtext-card" style="margin-top:14px">
                <div class="richtext-head">
                    <h3>{{ $block->title ?: 'Блок #' . $block->order_number }} <span class="badge badge-primary">{{ $block->type }}</span></h3>
                    <a href="{{ route('admin.content.lessoncontents.edit', $block) }}" class="btn btn-sm btn-warning">Изменить</a>
                </div>
                <div class="richtext-body">{!! $block->content !!}</div>
            </div>
        @empty
            <div class="empty-row" style="margin-top:14px">Содержание пока не добавлено. Нажмите «Добавить блок», чтобы создать его.</div>
        @endforelse
    </div>

    <div class="item-grid">
        <div class="card table-card">
            <div class="table-head">
                <h3>Слова <small>{{ $lesson->words->count() }}</small></h3>
                <a href="{{ route('admin.vocabulary.words.index') }}?lesson_id={{ $lesson->id }}" class="btn btn-sm btn-ghost">Все слова</a>
            </div>
            @if($lesson->words->isNotEmpty())
                <div class="table-scroll">
                    <table class="tbl">
                        <thead><tr><th>Слово</th><th>Перевод</th></tr></thead>
                        <tbody>
                        @foreach($lesson->words->take(10) as $word)
                            <tr>
                                <td class="title-cell">{{ $word->word }} <x-speak-button :word="$word->word" :audio-url="$word->audio_url" /></td>
                                <td class="text-cell">{{ $word->translations->first()?->translation ?? '—' }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="empty-row">Слова пока не добавлены.</div>
            @endif
        </div>

        <div class="card table-card">
            <div class="table-head">
                <h3>Упражнения <small>{{ $lesson->exercises->count() }}</small></h3>
                <a href="{{ route('admin.exercise.exercises.index') }}?lesson_id={{ $lesson->id }}" class="btn btn-sm btn-ghost">Все упражнения</a>
            </div>
            @if($lesson->exercises->isNotEmpty())
                <div class="table-scroll">
                    <table class="tbl">
                        <thead><tr><th>Название</th><th>Тип</th></tr></thead>
                        <tbody>
                        @foreach($lesson->exercises as $exercise)
                            <tr>
                                <td class="title-cell"><a class="chip-link" href="{{ route('admin.exercise.exercises.show', $exercise) }}">{{ $exercise->title }}</a></td>
                                <td><span class="badge badge-primary">{{ $exercise->type }}</span></td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="empty-row">Упражнения пока не добавлены.</div>
            @endif
        </div>

        <div class="card table-card">
            <div class="table-head">
                <h3>Тесты <small>{{ $lesson->tests->count() }}</small></h3>
                <a href="{{ route('admin.test.tests.index') }}?lesson_id={{ $lesson->id }}" class="btn btn-sm btn-ghost">Все тесты</a>
            </div>
            @if($lesson->tests->isNotEmpty())
                <div class="table-scroll">
                    <table class="tbl">
                        <thead><tr><th>Название</th><th>Проходной балл</th></tr></thead>
                        <tbody>
                        @foreach($lesson->tests as $test)
                            <tr>
                                <td class="title-cell"><a class="chip-link" href="{{ route('admin.test.tests.show', $test) }}">{{ $test->title }}</a></td>
                                <td>{{ $test->passing_score }}%</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="empty-row">Тесты пока не добавлены.</div>
            @endif
        </div>
    </div>
@endsection
