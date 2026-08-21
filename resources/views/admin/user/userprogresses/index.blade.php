@extends('layouts.admin')

@section('title', 'Прогресс пользователей')

@section('content')
    <x-admin.page-header title="Прогресс пользователей">
        <x-slot:actions>
            <a href="{{ route('admin.user.userprogresses.create') }}" class="btn btn-primary">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Добавить прогресс
            </a>
        </x-slot:actions>
    </x-admin.page-header>

    <div class="card">
        <form method="GET" class="filters">
            <select name="user_id" class="select">
                <option value="">Все пользователи</option>
                @foreach($users as $id => $name)
                    <option value="{{ $id }}" {{ request('user_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
                @endforeach
            </select>
            <select name="lesson_id" class="select">
                <option value="">Все уроки</option>
                @foreach($lessons as $id => $title)
                    <option value="{{ $id }}" {{ request('lesson_id') == $id ? 'selected' : '' }}>{{ $title }}</option>
                @endforeach
            </select>
            <select name="status" class="select">
                <option value="">Все статусы</option>
                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Завершено</option>
                <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>В процессе</option>
            </select>
            <button type="submit" class="btn btn-ghost">Фильтр</button>
            <a href="{{ route('admin.user.userprogresses.index') }}" class="btn btn-ghost">Сброс</a>
        </form>
    </div>

    <div class="card table-card">
        <div class="table-scroll">
            <table class="tbl">
                <thead>
                <tr>
                    <th style="width:70px">ID</th>
                    <th>Пользователь</th>
                    <th>Урок</th>
                    <th>Прогресс</th>
                    <th>Статус</th>
                    <th>Время</th>
                    <th>Позиция</th>
                    <th style="width:220px;text-align:right">Действия</th>
                </tr>
                </thead>
                <tbody>
                @forelse($progressRecords as $progress)
                    <tr>
                        <td class="id-cell">#{{ $progress->id }}</td>
                        <td>
                            @if($progress->user)
                                <a class="chip-link" href="{{ route('admin.user.users.show', $progress->user) }}">{{ $progress->user->name ?? $progress->user->email }}</a>
                            @else
                                <span class="id-cell">—</span>
                            @endif
                        </td>
                        <td>
                            @if($progress->lesson)
                                <a class="chip-link" href="{{ route('admin.content.lessons.show', $progress->lesson) }}">{{ Str::limit($progress->lesson->title, 30) }}</a>
                            @else
                                <span class="id-cell">—</span>
                            @endif
                        </td>
                        <td>
                            <div class="meter">
                                <div class="meter-track"><span class="meter-fill" style="width: {{ $progress->progress_percent }}%"></span></div>
                                <span>{{ $progress->progress_percent }}%</span>
                            </div>
                        </td>
                        <td>
                            @if($progress->is_completed)
                                <span class="badge badge-success">✓ Завершено</span>
                                <small style="display:block;color:var(--muted)">{{ $progress->completed_at?->format('d.m.Y') }}</small>
                            @else
                                <span class="badge badge-warning">В процессе</span>
                            @endif
                        </td>
                        <td>{{ floor($progress->time_spent / 60) }}м {{ $progress->time_spent % 60 }}с</td>
                        <td>{{ $progress->last_position }}</td>
                        <td>
                            <div class="actions">
                                <a href="{{ route('admin.user.userprogresses.show', $progress) }}" class="btn btn-sm btn-ghost">Просмотр</a>
                                <a href="{{ route('admin.user.userprogresses.edit', $progress) }}" class="btn btn-sm btn-warning">Изменить</a>
                                <form action="{{ route('admin.user.userprogresses.destroy', $progress) }}" method="POST" onsubmit="return confirm('Удалить запись прогресса?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">Удалить</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr class="empty-row"><td colspan="8">Записи прогресса не найдены</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if(method_exists($progressRecords, 'hasPages') && $progressRecords->hasPages())
        <div class="pagination">{{ $progressRecords->withQueryString()->links() }}</div>
    @endif
@endsection
