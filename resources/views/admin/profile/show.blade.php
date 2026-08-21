@extends('layouts.admin')

@section('title', 'Мой профиль')

@section('content')
    <x-admin.page-header title="Мой профиль">
        <x-slot:actions>
            <a href="{{ route('admin.profile.edit') }}" class="btn btn-warning">Изменить</a>
        </x-slot:actions>
    </x-admin.page-header>

    <div class="card icon-card">
        <div class="icon-card-wrap">
            <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 4-6 8-6s8 2 8 6"/></svg>
        </div>
        <div class="icon-card-title">{{ $user->name }}</div>
        <span class="badge badge-primary">{{ $user->role?->name ?? 'Администратор' }}</span>
    </div>

    <div class="card">
        <h3>Информация</h3>
        <div class="info-row"><span class="info-label">ID</span><span class="info-value">#{{ $user->id }}</span></div>
        <div class="info-row"><span class="info-label">Имя</span><span class="info-value" style="font-weight:700">{{ $user->name }}</span></div>
        <div class="info-row"><span class="info-label">Email</span><span class="info-value">{{ $user->email }}</span></div>
        <div class="info-row"><span class="info-label">Роль</span><span class="info-value">{{ $user->role?->name ?? '—' }}</span></div>
        <div class="info-row"><span class="info-label">Аккаунт создан</span><span class="info-value">{{ $user->created_at?->format('d.m.Y H:i') }}</span></div>
        <div class="info-row"><span class="info-label">Обновлён</span><span class="info-value">{{ $user->updated_at?->format('d.m.Y H:i') }}</span></div>
    </div>
@endsection
