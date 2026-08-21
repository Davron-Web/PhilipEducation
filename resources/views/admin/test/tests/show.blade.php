@extends('layouts.admin')

@section('title', 'Тест: ' . $test->title)

@section('content')
    @php
        $score = (int) ($test->passing_score ?? 0);
        $isPub = (bool) ($test->is_published ?? false);
        $questionsCount = $test->relationLoaded('questions') ? $test->questions->count() : $test->questions()->count();
    @endphp

    <x-admin.page-header :title="'Тест: ' . $test->title" :backRoute="route('admin.test.tests.index')" />

    <div class="show-grid">
        <div class="card icon-card">
            <div class="score-circle" style="--p: {{ $score }}">
                <div class="score-circle-inner">
                    <span class="score-circle-num">{{ $score }}%</span>
                    <span class="score-circle-lbl">Проходной</span>
                </div>
            </div>
            <div class="icon-card-title">{{ $test->title }}</div>
            <span class="status-pill {{ $isPub ? 'status-published' : 'status-draft' }}">
                <span class="status-dot"></span>
                {{ $isPub ? 'Опубликовано' : 'Черновик' }}
            </span>
        </div>

        <div class="card">
            <h3>Информация о тесте</h3>
            <div class="info-row"><span class="info-label">ID</span><span class="info-value">#{{ $test->id }}</span></div>
            <div class="info-row">
                <span class="info-label">Урок</span>
                <span class="info-value">
                    @if($test->lesson)
                        <a class="chip-link" href="{{ route('admin.content.lessons.show', $test->lesson) }}">{{ $test->lesson->title }}</a>
                    @else
                        —
                    @endif
                </span>
            </div>
            <div class="info-row"><span class="info-label">Проходной балл</span><span class="info-value" style="color:var(--blue)">{{ $score }}%</span></div>
            <div class="info-row"><span class="info-label">Лимит времени</span><span class="info-value">{{ $test->time_limit ? $test->time_limit . ' мин' : 'Без ограничений' }}</span></div>
            <div class="info-row"><span class="info-label">Вопросов</span><span class="info-value">{{ $questionsCount }}</span></div>
            <div class="info-row"><span class="info-label">Создано</span><span class="info-value">{{ optional($test->created_at)->format('d.m.Y H:i') ?? '—' }}</span></div>
            <div class="info-row"><span class="info-label">Обновлено</span><span class="info-value">{{ optional($test->updated_at)->format('d.m.Y H:i') ?? '—' }}</span></div>

            <div class="form-actions" style="margin-top:16px">
                <a href="{{ route('admin.test.tests.edit', $test) }}" class="btn btn-warning">Изменить</a>
                <form action="{{ route('admin.test.tests.destroy', $test) }}" method="POST" onsubmit="return confirm('Удалить тест «{{ addslashes($test->title ?? '') }}»?');">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-danger">Удалить</button>
                </form>
            </div>
        </div>
    </div>
@endsection
