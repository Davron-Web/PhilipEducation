@extends('layouts.admin')

@section('title', 'Достижения пользователей')

@section('content')
    <x-admin.page-header title="Достижения пользователей">
        <x-slot:actions>
            <a href="{{ route('admin.user.userachievements.create') }}" class="btn btn-primary">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Выдать достижение
            </a>
        </x-slot:actions>
    </x-admin.page-header>

    <div class="card">
        <form method="GET" class="filters">
            <select name="user_id" class="select">
                <option value="">Все пользователи</option>
                @foreach($users as $user)
                    <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                @endforeach
            </select>
            <select name="achievement_id" class="select">
                <option value="">Все достижения</option>
                @foreach($achievements as $achievement)
                    <option value="{{ $achievement->id }}" {{ request('achievement_id') == $achievement->id ? 'selected' : '' }}>{{ $achievement->title }}</option>
                @endforeach
            </select>
            <button type="submit" class="btn btn-ghost">Фильтр</button>
            <a href="{{ route('admin.user.userachievements.index') }}" class="btn btn-ghost">Сброс</a>
        </form>
    </div>

    <div class="card table-card">
        <div class="table-scroll">
            <table class="tbl">
                <thead>
                <tr>
                    <th style="width:70px">ID</th>
                    <th>Пользователь</th>
                    <th>Достижение</th>
                    <th>Баллы</th>
                    <th>Получено</th>
                    <th style="width:180px;text-align:right">Действия</th>
                </tr>
                </thead>
                <tbody>
                @forelse($userAchievements as $userAchievement)
                    <tr>
                        <td class="id-cell">#{{ $userAchievement->id }}</td>
                        <td>
                            @if($userAchievement->user)
                                <a class="chip-link" href="{{ route('admin.user.users.show', $userAchievement->user) }}">{{ $userAchievement->user->name ?? $userAchievement->user->email }}</a>
                            @else
                                <span class="id-cell">—</span>
                            @endif
                        </td>
                        <td>
                            @if($userAchievement->achievement)
                                <a class="chip-link" href="{{ route('admin.gamification.achievements.show', $userAchievement->achievement) }}">{{ $userAchievement->achievement->title }}</a>
                            @else
                                <span class="id-cell">—</span>
                            @endif
                        </td>
                        <td>
                            @if($userAchievement->achievement)
                                <span class="badge badge-primary">{{ $userAchievement->achievement->points }}</span>
                            @else
                                <span class="id-cell">—</span>
                            @endif
                        </td>
                        <td>{{ $userAchievement->earned_at?->format('d.m.Y H:i') }}</td>
                        <td>
                            <div class="actions">
                                <a href="{{ route('admin.user.userachievements.show', $userAchievement) }}" class="btn btn-sm btn-ghost">Просмотр</a>
                                <form action="{{ route('admin.user.userachievements.destroy', $userAchievement) }}" method="POST" onsubmit="return confirm('Отозвать достижение?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">Отозвать</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr class="empty-row"><td colspan="6">Достижения ещё не выданы</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if(method_exists($userAchievements, 'hasPages') && $userAchievements->hasPages())
        <div class="pagination">{{ $userAchievements->withQueryString()->links() }}</div>
    @endif
@endsection
