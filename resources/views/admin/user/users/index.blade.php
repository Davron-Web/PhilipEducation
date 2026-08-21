@extends('layouts.admin')

@section('title', 'Пользователи')

@section('content')
    <x-admin.page-header title="Пользователи">
        <x-slot:actions>
            <a href="{{ route('admin.user.users.create') }}" class="btn btn-primary">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Добавить пользователя
            </a>
        </x-slot:actions>
    </x-admin.page-header>

    <div class="card">
        <form method="GET" class="filters">
            <input type="text" name="search" class="input" placeholder="Поиск..." value="{{ request('search') }}">
            <select name="role_id" class="select">
                <option value="">Все роли</option>
                @foreach(\App\Models\User\Role::all() as $role)
                    <option value="{{ $role->id }}" {{ request('role_id') == $role->id ? 'selected' : '' }}>{{ $role->name }}</option>
                @endforeach
            </select>
            <button type="submit" class="btn btn-ghost">Фильтр</button>
            <a href="{{ route('admin.user.users.index') }}" class="btn btn-ghost">Сброс</a>
        </form>
    </div>

    <div class="card table-card">
        <div class="table-scroll">
            <table class="tbl">
                <thead>
                <tr>
                    <th style="width:70px">ID</th>
                    <th>Имя</th>
                    <th>Email</th>
                    <th>Роль</th>
                    <th>Уровень</th>
                    <th>Баллы</th>
                    <th style="width:110px">Активен</th>
                    <th style="width:220px;text-align:right">Действия</th>
                </tr>
                </thead>
                <tbody>
                @forelse($users as $user)
                    <tr>
                        <td class="id-cell">#{{ $user->id }}</td>
                        <td class="title-cell">{{ $user->name }}</td>
                        <td class="text-cell">{{ $user->email }}</td>
                        <td>{{ $user->role?->name ?? '—' }}</td>
                        <td>{{ $user->level?->code ?? '—' }}</td>
                        <td><span class="badge badge-primary">{{ $user->points }}</span></td>
                        <td>
                            @if($user->is_active)
                                <span class="badge badge-success">Да</span>
                            @else
                                <span class="badge badge-secondary">Нет</span>
                            @endif
                        </td>
                        <td>
                            <div class="actions">
                                <a href="{{ route('admin.user.users.show', $user) }}" class="btn btn-sm btn-ghost">Просмотр</a>
                                <a href="{{ route('admin.user.users.edit', $user) }}" class="btn btn-sm btn-warning">Изменить</a>
                                <form action="{{ route('admin.user.users.destroy', $user) }}" method="POST" onsubmit="return confirm('Удалить пользователя?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">Удалить</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr class="empty-row"><td colspan="8">Пользователи не найдены</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if(method_exists($users, 'hasPages') && $users->hasPages())
        <div class="pagination">{{ $users->withQueryString()->links() }}</div>
    @endif
@endsection
