@extends('layouts.admin')

@section('title', 'Титулы')

@section('content')
    <x-admin.page-header title="Титулы">
        <x-slot:actions>
            <a href="{{ route('admin.gamification.titles.create') }}" class="btn btn-primary">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Добавить титул
            </a>
        </x-slot:actions>
    </x-admin.page-header>

    <div class="card">
        <form method="GET" class="filters">
            <input type="text" name="search" class="input" placeholder="Поиск по названию..." value="{{ request('search') }}">
            <button type="submit" class="btn btn-ghost">Фильтр</button>
            <a href="{{ route('admin.gamification.titles.index') }}" class="btn btn-ghost">Сброс</a>
        </form>
    </div>

    <div class="card table-card">
        <div class="table-scroll">
            <table class="tbl">
                <thead>
                <tr>
                    <th style="width:70px">ID</th>
                    <th style="width:70px">Значок</th>
                    <th>Название</th>
                    <th>Описание</th>
                    <th style="width:110px">Порог XP</th>
                    <th style="width:100px">Получили</th>
                    <th style="width:100px">Статус</th>
                    <th style="width:220px;text-align:right">Действия</th>
                </tr>
                </thead>
                <tbody>
                @forelse($titles as $title)
                    <tr>
                        <td class="id-cell">#{{ $title->id }}</td>
                        <td style="font-size:20px">{{ $title->icon ?: '—' }}</td>
                        <td class="title-cell">{{ $title->name }}</td>
                        <td class="text-cell">{{ $title->description ? Str::limit($title->description, 60) : '—' }}</td>
                        <td><span class="badge badge-success">{{ number_format($title->min_xp, 0, ',', ' ') }}</span></td>
                        <td>{{ $title->users_count }}</td>
                        <td>
                            <span class="badge badge-{{ $title->is_active ? 'success' : 'warning' }}">
                                {{ $title->is_active ? 'Активен' : 'Скрыт' }}
                            </span>
                        </td>
                        <td>
                            <div class="actions">
                                <a href="{{ route('admin.gamification.titles.show', $title) }}" class="btn btn-sm btn-ghost">Просмотр</a>
                                <a href="{{ route('admin.gamification.titles.edit', $title) }}" class="btn btn-sm btn-warning">Изменить</a>
                                <form action="{{ route('admin.gamification.titles.destroy', $title) }}" method="POST" onsubmit="return confirm('Удалить титул?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">Удалить</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr class="empty-row"><td colspan="8">Титулы не найдены</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($titles->hasPages())
        <div class="pagination">{{ $titles->withQueryString()->links() }}</div>
    @endif
@endsection
