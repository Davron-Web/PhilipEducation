@extends('layouts.admin')

@section('title', 'Достижения')

@section('content')
    <x-admin.page-header title="Достижения">
        <x-slot:actions>
            <a href="{{ route('admin.gamification.achievements.create') }}" class="btn btn-primary">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Добавить достижение
            </a>
        </x-slot:actions>
    </x-admin.page-header>

    <div class="card">
        <form method="GET" class="filters">
            <input type="text" name="search" class="input" placeholder="Поиск по названию или описанию..." value="{{ request('search') }}">
            <select name="min_points" class="select">
                <option value="">Все баллы</option>
                <option value="0" {{ request('min_points') === '0' ? 'selected' : '' }}>0+ баллов</option>
                <option value="10" {{ request('min_points') == '10' ? 'selected' : '' }}>10+ баллов</option>
                <option value="50" {{ request('min_points') == '50' ? 'selected' : '' }}>50+ баллов</option>
                <option value="100" {{ request('min_points') == '100' ? 'selected' : '' }}>100+ баллов</option>
            </select>
            <button type="submit" class="btn btn-ghost">Фильтр</button>
            <a href="{{ route('admin.gamification.achievements.index') }}" class="btn btn-ghost">Сброс</a>
        </form>
    </div>

    <div class="card table-card">
        <div class="table-scroll">
            <table class="tbl">
                <thead>
                <tr>
                    <th style="width:70px">ID</th>
                    <th style="width:80px">Иконка</th>
                    <th>Название</th>
                    <th>Описание</th>
                    <th style="width:110px">Баллы</th>
                    <th style="width:220px;text-align:right">Действия</th>
                </tr>
                </thead>
                <tbody>
                @forelse($achievements as $achievement)
                    <tr>
                        <td class="id-cell">#{{ $achievement->id }}</td>
                        <td>
                            @if($achievement->icon && (str_starts_with($achievement->icon, 'http') || str_starts_with($achievement->icon, '/')))
                                <img src="{{ $achievement->icon }}" alt="{{ $achievement->title }}" class="media-thumb" style="width:40px;height:40px;border-radius:8px;">
                            @else
                                <div class="media-thumb-empty" style="width:40px;height:40px;border-radius:8px;">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M8 21h8M12 17v4M7 4h10v5a5 5 0 0 1-10 0V4ZM7 4H4a3 3 0 0 0 3 4M17 4h3a3 3 0 0 1-3 4"/></svg>
                                </div>
                            @endif
                        </td>
                        <td class="title-cell">{{ $achievement->title }}</td>
                        <td class="text-cell">{{ $achievement->description ? Str::limit($achievement->description, 60) : '—' }}</td>
                        <td>
                            <span class="badge badge-{{ $achievement->points >= 100 ? 'danger' : ($achievement->points >= 50 ? 'warning' : 'success') }}">{{ $achievement->points }}</span>
                        </td>
                        <td>
                            <div class="actions">
                                <a href="{{ route('admin.gamification.achievements.show', $achievement) }}" class="btn btn-sm btn-ghost">Просмотр</a>
                                <a href="{{ route('admin.gamification.achievements.edit', $achievement) }}" class="btn btn-sm btn-warning">Изменить</a>
                                <form action="{{ route('admin.gamification.achievements.destroy', $achievement) }}" method="POST" onsubmit="return confirm('Удалить достижение?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">Удалить</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr class="empty-row"><td colspan="6">Достижения не найдены</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if(method_exists($achievements, 'hasPages') && $achievements->hasPages())
        <div class="pagination">{{ $achievements->withQueryString()->links() }}</div>
    @endif
@endsection
