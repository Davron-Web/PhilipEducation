@extends('layouts.admin')

@section('title', 'Чат: жалобы')

@section('content')
    <x-admin.page-header title="Чат: жалобы" />

    <div class="card table-card">
        <div class="table-scroll">
            <table class="tbl">
                <thead>
                <tr>
                    <th style="width:70px">ID</th>
                    <th>Сообщение</th>
                    <th style="width:180px">Автор сообщения</th>
                    <th style="width:180px">Пожаловался</th>
                    <th style="width:150px">Дата</th>
                    <th style="width:320px;text-align:right">Действия</th>
                </tr>
                </thead>
                <tbody>
                @forelse($reports as $report)
                    <tr>
                        <td class="id-cell">#{{ $report->id }}</td>
                        <td class="text-cell">
                            @if ($report->message && ! $report->message->is_deleted)
                                {{ Str::limit($report->message->body, 80) }}
                            @else
                                <span style="color:var(--muted)">сообщение уже скрыто</span>
                            @endif
                        </td>
                        <td class="title-cell">
                            @if ($report->message?->user)
                                <a href="{{ route('admin.user.users.show', $report->message->user) }}">{{ $report->message->user->name }}</a>
                            @else
                                —
                            @endif
                        </td>
                        <td class="title-cell">{{ $report->reporter->name ?? '—' }}</td>
                        <td>{{ $report->created_at->format('d.m.Y H:i') }}</td>
                        <td>
                            <div class="actions">
                                @if ($report->message && ! $report->message->is_deleted)
                                    <form action="{{ route('admin.chat.messages.destroy', $report->message) }}" method="POST" onsubmit="return confirm('Скрыть сообщение?');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">Скрыть сообщение</button>
                                    </form>
                                @endif

                                @if ($report->message?->user && ! $report->message->user->isChatBanned())
                                    <form action="{{ route('admin.chat.users.ban', $report->message->user) }}" method="POST" onsubmit="return confirm('Заблокировать автора в чате?');">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-warning">Заблокировать автора</button>
                                    </form>
                                @endif

                                <form action="{{ route('admin.chat.reports.destroy', $report) }}" method="POST" onsubmit="return confirm('Снять жалобу с рассмотрения?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-ghost">Отклонить</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr class="empty-row"><td colspan="6">Жалоб пока нет</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if ($reports->hasPages())
        <div class="pagination">{{ $reports->withQueryString()->links() }}</div>
    @endif
@endsection
