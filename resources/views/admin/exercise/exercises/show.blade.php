@extends('layouts.admin')

@section('title', 'Упражнение: ' . $exercise->title)

@section('content')
    @php
        $exId    = $exercise->id ?? '';
        $exTitle = $exercise->title ?? 'Без названия';
        $exLesson = $exercise->lesson ?? null;
        $exInstr = (string) ($exercise->instructions ?? '');
        $exLabel = $exercise->type_label ?? ($exercise->type ?? '—');
        $exColor = $exercise->type_badge_color ?? 'secondary';
        $allowedColors = ['primary', 'secondary', 'success', 'info', 'warning', 'danger', 'light', 'dark'];
        if (!in_array($exColor, $allowedColors, true)) $exColor = 'secondary';

        $hasInstr  = strlen(trim(strip_tags($exInstr))) > 0;
        $charCount = mb_strlen(strip_tags($exInstr));
    @endphp

    <x-admin.page-header :title="'Упражнение: ' . $exTitle">
        <x-slot:sub><span class="live-dot"></span> ID: <b style="color:var(--text);margin-left:4px">#{{ $exId }}</b></x-slot:sub>
        <x-slot:actions>
            <a href="{{ route('admin.exercise.exercises.index') }}" class="btn btn-ghost">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
                К списку
            </a>
        </x-slot:actions>
    </x-admin.page-header>

    <div class="show-grid">
        <div class="card icon-card">
            <div class="icon-card-wrap">
                <svg width="56" height="56" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"/>
                    <path d="M15 5l4 4"/>
                </svg>
            </div>
            <div class="icon-card-title">{{ $exTitle }}</div>
            <span class="badge badge-{{ $exColor }}">{{ $exLabel }}</span>
        </div>

        <div class="card">
            <h3>Информация об упражнении</h3>
            <div class="info-row"><span class="info-label">ID</span><span class="info-value">#{{ $exId }}</span></div>
            <div class="info-row">
                <span class="info-label">Урок</span>
                <span class="info-value">
                    @if($exLesson)
                        <a class="chip-link" href="{{ route('admin.content.lessons.show', $exLesson) }}">{{ $exLesson->title ?? 'Урок' }}</a>
                    @else
                        —
                    @endif
                </span>
            </div>
            <div class="info-row"><span class="info-label">Тип</span><span class="info-value"><span class="badge badge-{{ $exColor }}">{{ $exLabel }}</span></span></div>
            <div class="info-row"><span class="info-label">Создано</span><span class="info-value">{{ optional($exercise->created_at)->format('d.m.Y H:i') ?? '—' }}</span></div>
            <div class="info-row"><span class="info-label">Обновлено</span><span class="info-value">{{ optional($exercise->updated_at)->format('d.m.Y H:i') ?? '—' }}</span></div>

            <div class="form-actions" style="margin-top:16px">
                <a href="{{ route('admin.exercise.exercises.edit', $exercise) }}" class="btn btn-warning">Изменить</a>
                <form action="{{ route('admin.exercise.exercises.destroy', $exercise) }}" method="POST" onsubmit="return confirm('Удалить упражнение «{{ addslashes($exTitle) }}»?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Удалить</button>
                </form>
            </div>
        </div>
    </div>

    <div class="card richtext-card">
        <div class="richtext-head">
            <h3>Инструкции</h3>
            @if($hasInstr)
                <span class="badge-count">{{ number_format($charCount, 0, ',', ' ') }} симв.</span>
            @endif
        </div>
        <div class="richtext-body">
            @if($hasInstr)
                {!! $exInstr !!}
            @else
                <div class="richtext-empty">Инструкции для этого упражнения ещё не заданы</div>
            @endif
        </div>
    </div>
@endsection
