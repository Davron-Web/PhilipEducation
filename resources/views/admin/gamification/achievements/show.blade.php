@extends('layouts.admin')

@section('title', 'Достижение: ' . $achievement->title)

@section('content')
    <x-admin.page-header :title="'Достижение: ' . $achievement->title" :backRoute="route('admin.gamification.achievements.index')">
        <x-slot:actions>
            <a href="{{ route('admin.gamification.achievements.edit', $achievement) }}" class="btn btn-warning">Изменить</a>
        </x-slot:actions>
    </x-admin.page-header>

    <div class="card icon-card">
        @if($achievement->icon && (str_starts_with($achievement->icon, 'http') || str_starts_with($achievement->icon, '/')))
            <img src="{{ $achievement->icon }}" alt="{{ $achievement->title }}" class="media-thumb" style="width:120px;height:120px;border-radius:24px;">
        @else
            <div class="icon-card-wrap">
                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M8 21h8M12 17v4M7 4h10v5a5 5 0 0 1-10 0V4ZM7 4H4a3 3 0 0 0 3 4M17 4h3a3 3 0 0 1-3 4"/></svg>
            </div>
        @endif
        <div class="icon-card-title">{{ $achievement->title }}</div>
        <span class="badge badge-{{ $achievement->points >= 100 ? 'danger' : ($achievement->points >= 50 ? 'warning' : 'success') }}">{{ $achievement->points }} баллов</span>
    </div>

    <div class="card">
        <h3>Информация</h3>
        <div class="info-row"><span class="info-label">ID</span><span class="info-value">#{{ $achievement->id }}</span></div>
        <div class="info-row"><span class="info-label">Название</span><span class="info-value" style="font-weight:700">{{ $achievement->title }}</span></div>
        <div class="info-row"><span class="info-label">Описание</span><span class="info-value">{{ $achievement->description ?? '—' }}</span></div>
        <div class="info-row"><span class="info-label">Баллы</span><span class="info-value">{{ $achievement->points }}</span></div>
        <div class="info-row"><span class="info-label">Получили пользователи</span><span class="info-value">{{ $achievement->users->count() }}</span></div>
        <div class="info-row"><span class="info-label">Создано</span><span class="info-value">{{ optional($achievement->created_at)->format('d.m.Y H:i') }}</span></div>
        <div class="info-row"><span class="info-label">Обновлено</span><span class="info-value">{{ optional($achievement->updated_at)->format('d.m.Y H:i') }}</span></div>

        <div class="form-actions" style="margin-top:16px">
            <form action="{{ route('admin.gamification.achievements.destroy', $achievement) }}" method="POST" onsubmit="return confirm('Удалить достижение?');">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-danger">Удалить достижение</button>
            </form>
        </div>
    </div>
@endsection
