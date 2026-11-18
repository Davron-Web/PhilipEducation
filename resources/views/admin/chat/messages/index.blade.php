@extends('layouts.admin')

@section('title', 'Чат: сообщения')

@section('content')
    <x-admin.page-header title="Чат: сообщения" />

    <div class="card">
        <form method="GET" class="filters">
            <input type="text" name="search" class="input" placeholder="Поиск по тексту сообщения..." value="{{ request('search') }}">
            <button type="submit" class="btn btn-ghost">Фильтр</button>
            <a href="{{ route('admin.chat.messages.index') }}" class="btn btn-ghost">Сброс</a>
        </form>
    </div>

    <div class="card table-card">
        <div class="table-scroll">
            <table class="tbl">
                <thead>
                <tr>
                    <th style="width:70px">ID</th>
                    <th style="width:200px">Автор</th>
                    <th>Текст</th>
                    <th style="width:150px">Отправлено</th>
                    <th style="width:110px">Статус</th>
                    <th style="width:260px;text-align:right">Действия</th>
                </tr>
                </thead>
                <tbody>
                @forelse($messages as $message)
                    <tr>
                        <td class="id-cell">#{{ $message->id }}</td>
                        <td class="title-cell">
                            <a href="{{ route('admin.user.users.show', $message->user) }}">{{ $message->user->name }}</a>
                            @if ($message->user->isChatBanned())
                                <span class="badge badge-danger">забанен</span>
                            @endif
                        </td>
                        <td class="text-cell">{{ Str::limit($message->body, 80) }}</td>
                        <td>{{ $message->created_at->format('d.m.Y H:i') }}</td>
                        <td>
                            @if ($message->is_deleted)
                                <span class="badge badge-danger">скрыто</span>
                            @else
                                <span class="badge badge-success">активно</span>
                            @endif
                        </td>
                        <td>
                            <div class="actions">
                                @unless ($message->is_deleted)
                                    <form action="{{ route('admin.chat.messages.destroy', $message) }}" method="POST" onsubmit="return confirm('Скрыть сообщение?');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">Скрыть</button>
                                    </form>
                                @endunless

                                @if ($message->user->isChatBanned())
                                    <form action="{{ route('admin.chat.users.unban', $message->user) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-ghost">Разбанить автора</button>
                                    </form>
                                @else
                                    <form action="{{ route('admin.chat.users.ban', $message->user) }}" method="POST" onsubmit="return confirm('Заблокировать автора в чате?');">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-warning">Заблокировать автора</button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr class="empty-row"><td colspan="6">Сообщений пока нет</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if ($messages->hasPages())
        <div class="pagination">{{ $messages->withQueryString()->links() }}</div>
    @endif
@endsection
