@extends('layouts.admin')

@section('title', 'Достижение пользователя #' . $userAchievement->id)

@section('content')
    <x-admin.page-header :title="'Достижение #' . $userAchievement->id" :backRoute="route('admin.user.userachievements.index')" />

    @if($userAchievement->achievement)
        <div class="card icon-card">
            @if($userAchievement->achievement->icon && (str_starts_with($userAchievement->achievement->icon, 'http') || str_starts_with($userAchievement->achievement->icon, '/')))
                <img src="{{ $userAchievement->achievement->icon }}" alt="{{ $userAchievement->achievement->title }}" class="media-thumb" style="width:100px;height:100px;border-radius:20px;">
            @else
                <div class="icon-card-wrap">
                    <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M8 21h8M12 17v4M7 4h10v5a5 5 0 0 1-10 0V4ZM7 4H4a3 3 0 0 0 3 4M17 4h3a3 3 0 0 1-3 4"/></svg>
                </div>
            @endif
            <div class="icon-card-title">{{ $userAchievement->achievement->title }}</div>
            <span class="badge badge-primary">{{ $userAchievement->achievement->points }} баллов</span>
        </div>
    @endif

    <div class="card">
        <h3>Информация</h3>
        <div class="info-row"><span class="info-label">ID</span><span class="info-value">#{{ $userAchievement->id }}</span></div>
        <div class="info-row">
            <span class="info-label">Пользователь</span>
            <span class="info-value">
                @if($userAchievement->user)
                    <a class="chip-link" href="{{ route('admin.user.users.show', $userAchievement->user) }}">{{ $userAchievement->user->name ?? $userAchievement->user->email }}</a>
                @else
                    —
                @endif
            </span>
        </div>
        <div class="info-row">
            <span class="info-label">Достижение</span>
            <span class="info-value">
                @if($userAchievement->achievement)
                    <a class="chip-link" href="{{ route('admin.gamification.achievements.show', $userAchievement->achievement) }}">{{ $userAchievement->achievement->title }}</a>
                @else
                    —
                @endif
            </span>
        </div>
        <div class="info-row"><span class="info-label">Описание</span><span class="info-value">{{ $userAchievement->achievement?->description ?? '—' }}</span></div>
        <div class="info-row"><span class="info-label">Получено</span><span class="info-value">{{ $userAchievement->earned_at?->format('d.m.Y H:i') }}</span></div>
        <div class="info-row"><span class="info-label">Выдано</span><span class="info-value">{{ $userAchievement->created_at?->format('d.m.Y H:i') }}</span></div>

        <div class="form-actions" style="margin-top:16px">
            <form action="{{ route('admin.user.userachievements.destroy', $userAchievement) }}" method="POST" onsubmit="return confirm('Отозвать достижение?');">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-danger">Отозвать достижение</button>
            </form>
        </div>
    </div>
@endsection
