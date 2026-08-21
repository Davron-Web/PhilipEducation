@extends('layouts.admin')

@section('title', 'Пользователь: ' . $user->name)

@section('content')
    <x-admin.page-header :title="$user->name" :backRoute="route('admin.user.users.index')">
        <x-slot:actions>
            <a href="{{ route('admin.user.users.edit', $user) }}" class="btn btn-warning">Изменить</a>
        </x-slot:actions>
    </x-admin.page-header>

    <div class="card">
        <h3>Информация</h3>
        <div class="info-row"><span class="info-label">ID</span><span class="info-value">#{{ $user->id }}</span></div>
        <div class="info-row"><span class="info-label">Имя</span><span class="info-value" style="font-weight:700">{{ $user->name }}</span></div>
        <div class="info-row"><span class="info-label">Email</span><span class="info-value">{{ $user->email }}</span></div>
        <div class="info-row"><span class="info-label">Роль</span><span class="info-value">{{ $user->role?->name ?? '—' }}</span></div>
        <div class="info-row"><span class="info-label">Уровень</span><span class="info-value">{{ $user->level?->code ?? '—' }}</span></div>
        <div class="info-row"><span class="info-label">Баллы</span><span class="info-value"><span class="badge badge-primary">{{ $user->points }}</span></span></div>
        <div class="info-row">
            <span class="info-label">Активен</span>
            <span class="info-value">
                @if($user->is_active)
                    <span class="badge badge-success">Да</span>
                @else
                    <span class="badge badge-secondary">Нет</span>
                @endif
            </span>
        </div>
        <div class="info-row"><span class="info-label">Создан</span><span class="info-value">{{ $user->created_at }}</span></div>
        <div class="info-row"><span class="info-label">Обновлён</span><span class="info-value">{{ $user->updated_at }}</span></div>
    </div>

    <div class="stats-grid">
        <x-admin.stat-card label="Уроков пройдено" :value="$user->progress->where('is_completed', true)->count()" color="blue" />
        <x-admin.stat-card label="Тестов сдано" :value="$user->results->where('passed', true)->count()" color="green" />
        <x-admin.stat-card label="Достижений" :value="$user->achievements->count()" color="yellow" />
    </div>
@endsection
