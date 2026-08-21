@extends('layouts.admin')

@section('title', 'Вопрос #' . $exerciseQuestion->id)

@section('content')
    <x-admin.page-header :title="'Вопрос #' . $exerciseQuestion->id" :backRoute="route('admin.exercise.exercisequestions.index')">
        <x-slot:actions>
            <a href="{{ route('admin.exercise.exercisequestions.edit', $exerciseQuestion) }}" class="btn btn-warning">Изменить</a>
        </x-slot:actions>
    </x-admin.page-header>

    <div class="card">
        <h3>Информация</h3>
        <div class="info-row"><span class="info-label">ID</span><span class="info-value">#{{ $exerciseQuestion->id }}</span></div>
        <div class="info-row">
            <span class="info-label">Упражнение</span>
            <span class="info-value">
                @if($exerciseQuestion->exercise)
                    <a class="chip-link" href="{{ route('admin.exercise.exercises.show', $exerciseQuestion->exercise) }}">{{ $exerciseQuestion->exercise->title }}</a>
                @else
                    —
                @endif
            </span>
        </div>
        <div class="info-row"><span class="info-label">Создано</span><span class="info-value">{{ optional($exerciseQuestion->created_at)->format('d.m.Y H:i') }}</span></div>
        <div class="info-row"><span class="info-label">Обновлено</span><span class="info-value">{{ optional($exerciseQuestion->updated_at)->format('d.m.Y H:i') }}</span></div>
    </div>

    <div class="card richtext-card">
        <div class="richtext-head"><h3>Вопрос</h3></div>
        <div class="richtext-body">{!! nl2br(e($exerciseQuestion->question)) !!}</div>
    </div>

    <div class="card richtext-card">
        <div class="richtext-head"><h3>Правильный ответ</h3></div>
        <div class="richtext-body" style="color:var(--green)">{!! nl2br(e($exerciseQuestion->correct_answer)) !!}</div>
    </div>

    <form action="{{ route('admin.exercise.exercisequestions.destroy', $exerciseQuestion) }}" method="POST" onsubmit="return confirm('Удалить вопрос?');">
        @csrf @method('DELETE')
        <button type="submit" class="btn btn-danger">Удалить вопрос</button>
    </form>
@endsection
