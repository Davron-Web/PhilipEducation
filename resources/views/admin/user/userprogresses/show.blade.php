@extends('layouts.admin')

@section('title', 'Прогресс #' . $userProgress->id)

@section('content')
    <x-admin.page-header :title="'Прогресс #' . $userProgress->id" :backRoute="route('admin.user.userprogresses.index')">
        <x-slot:badge>
            @if($userProgress->is_completed)
                <span class="badge badge-success">✓ Завершено</span>
            @else
                <span class="badge badge-warning">В процессе</span>
            @endif
        </x-slot:badge>
        <x-slot:actions>
            <a href="{{ route('admin.user.userprogresses.edit', $userProgress) }}" class="btn btn-warning">Изменить</a>
        </x-slot:actions>
    </x-admin.page-header>

    <div class="card">
        <h3>Информация</h3>
        <div class="info-row"><span class="info-label">ID</span><span class="info-value">#{{ $userProgress->id }}</span></div>
        <div class="info-row">
            <span class="info-label">Пользователь</span>
            <span class="info-value">
                @if($userProgress->user)
                    <a class="chip-link" href="{{ route('admin.user.users.show', $userProgress->user) }}">{{ $userProgress->user->name ?? $userProgress->user->email }}</a>
                @else
                    —
                @endif
            </span>
        </div>
        <div class="info-row">
            <span class="info-label">Урок</span>
            <span class="info-value">
                @if($userProgress->lesson)
                    <a class="chip-link" href="{{ route('admin.content.lessons.show', $userProgress->lesson) }}">{{ $userProgress->lesson->title }}</a>
                @else
                    —
                @endif
            </span>
        </div>
        <div class="info-row">
            <span class="info-label">Прогресс</span>
            <span class="info-value">
                <div class="meter">
                    <div class="meter-track"><span class="meter-fill" style="width: {{ $userProgress->progress_percent }}%"></span></div>
                    <span>{{ $userProgress->progress_percent }}%</span>
                </div>
            </span>
        </div>
        <div class="info-row">
            <span class="info-label">Время</span>
            <span class="info-value">
                @php
                    $hours = floor($userProgress->time_spent / 3600);
                    $minutes = floor(($userProgress->time_spent % 3600) / 60);
                    $seconds = $userProgress->time_spent % 60;
                @endphp
                @if($hours > 0)
                    {{ $hours }}ч {{ $minutes }}м {{ $seconds }}с
                @elseif($minutes > 0)
                    {{ $minutes }}м {{ $seconds }}с
                @else
                    {{ $seconds }}с
                @endif
            </span>
        </div>
        <div class="info-row"><span class="info-label">Последняя позиция</span><span class="info-value">{{ $userProgress->last_position }}</span></div>
        <div class="info-row"><span class="info-label">Дата завершения</span><span class="info-value">{{ $userProgress->completed_at?->format('d.m.Y H:i') ?? '—' }}</span></div>
        <div class="info-row"><span class="info-label">Создано</span><span class="info-value">{{ $userProgress->created_at?->format('d.m.Y H:i') }}</span></div>
        <div class="info-row"><span class="info-label">Обновлено</span><span class="info-value">{{ $userProgress->updated_at?->format('d.m.Y H:i') }}</span></div>

        <div class="form-actions" style="margin-top:16px">
            <form action="{{ route('admin.user.userprogresses.destroy', $userProgress) }}" method="POST" onsubmit="return confirm('Удалить запись прогресса?');">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-danger">Удалить прогресс</button>
            </form>
        </div>
    </div>
@endsection
