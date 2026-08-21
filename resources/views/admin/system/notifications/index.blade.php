@extends('layouts.admin')

@section('title', 'Уведомления')

@section('content')
    <x-admin.page-header title="Уведомления">
        <x-slot:actions>
            <a href="{{ route('admin.system.notifications.create') }}" class="btn btn-primary">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Отправить уведомление
            </a>
        </x-slot:actions>
    </x-admin.page-header>

    <div class="card">
        <form method="GET" class="filters">
            <input type="text" name="search" class="input" placeholder="Поиск по названию или тексту..." value="{{ request('search') }}">
            <select name="user_id" class="select">
                <option value="">Все пользователи</option>
                @foreach($users as $user)
                    <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                @endforeach
            </select>
            <select name="status" class="select">
                <option value="">Все статусы</option>
                <option value="read" {{ request('status') == 'read' ? 'selected' : '' }}>Прочитано</option>
                <option value="unread" {{ request('status') == 'unread' ? 'selected' : '' }}>Не прочитано</option>
            </select>
            <button type="submit" class="btn btn-ghost">Фильтр</button>
            <a href="{{ route('admin.system.notifications.index') }}" class="btn btn-ghost">Сброс</a>
        </form>
    </div>

    <div class="card table-card">
        <div class="table-scroll">
            <table class="tbl">
                <thead>
                <tr>
                    <th style="width:70px">ID</th>
                    <th>Пользователь</th>
                    <th>Название</th>
                    <th>Сообщение</th>
                    <th style="width:120px">Статус</th>
                    <th>Прочитано</th>
                    <th>Отправлено</th>
                    <th style="width:260px;text-align:right">Действия</th>
                </tr>
                </thead>
                <tbody>
                @forelse($notifications as $notification)
                    <tr>
                        <td class="id-cell">#{{ $notification->id }}</td>
                        <td>
                            @if($notification->user)
                                <a class="chip-link" href="{{ route('admin.user.users.show', $notification->user) }}">{{ $notification->user->name ?? $notification->user->email }}</a>
                            @else
                                <span class="id-cell">—</span>
                            @endif
                        </td>
                        <td class="title-cell">{{ $notification->title }}</td>
                        <td class="text-cell">{{ Str::limit($notification->message, 50) }}</td>
                        <td>
                            @if($notification->is_read)
                                <span class="badge badge-success">Прочитано</span>
                            @else
                                <span class="badge badge-warning">Не прочитано</span>
                            @endif
                        </td>
                        <td>{{ $notification->read_at?->format('d.m.Y H:i') ?? '—' }}</td>
                        <td>{{ $notification->created_at?->format('d.m.Y H:i') }}</td>
                        <td>
                            <div class="actions">
                                <a href="{{ route('admin.system.notifications.show', $notification) }}" class="btn btn-sm btn-ghost">Просмотр</a>
                                @if(!$notification->is_read)
                                    <form action="{{ route('admin.system.notifications.mark-read', $notification) }}" method="POST">
                                        @csrf @method('PATCH')
                                        <button type="submit" class="btn btn-sm btn-accent">Отметить</button>
                                    </form>
                                @endif
                                <form action="{{ route('admin.system.notifications.destroy', $notification) }}" method="POST" onsubmit="return confirm('Удалить уведомление?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">Удалить</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr class="empty-row"><td colspan="8">Уведомления не найдены</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if(method_exists($notifications, 'hasPages') && $notifications->hasPages())
        <div class="pagination">{{ $notifications->withQueryString()->links() }}</div>
    @endif
@endsection
