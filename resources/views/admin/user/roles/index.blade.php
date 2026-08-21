@extends('layouts.admin')

@section('title', 'Роли')

@section('content')
    <x-admin.page-header title="Роли">
        <x-slot:actions>
            <a href="{{ route('admin.user.roles.create') }}" class="btn btn-primary">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Добавить роль
            </a>
        </x-slot:actions>
    </x-admin.page-header>

    <div class="card table-card">
        <div class="table-scroll">
            <table class="tbl">
                <thead>
                <tr>
                    <th style="width:70px">ID</th>
                    <th>Название</th>
                    <th>Описание</th>
                    <th>Пользователей</th>
                    <th style="width:220px;text-align:right">Действия</th>
                </tr>
                </thead>
                <tbody>
                @forelse($roles as $role)
                    <tr>
                        <td class="id-cell">#{{ $role->id }}</td>
                        <td class="title-cell">{{ $role->name }}</td>
                        <td class="text-cell">{{ $role->description ?? '—' }}</td>
                        <td><span class="badge badge-primary">{{ $role->users_count ?? $role->users()->count() }}</span></td>
                        <td>
                            <div class="actions">
                                <a href="{{ route('admin.user.roles.show', $role) }}" class="btn btn-sm btn-ghost">Просмотр</a>
                                <a href="{{ route('admin.user.roles.edit', $role) }}" class="btn btn-sm btn-warning">Изменить</a>
                                <form action="{{ route('admin.user.roles.destroy', $role) }}" method="POST" onsubmit="return confirm('Удалить роль?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">Удалить</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr class="empty-row"><td colspan="5">Роли не найдены</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if(method_exists($roles, 'hasPages') && $roles->hasPages())
        <div class="pagination">{{ $roles->withQueryString()->links() }}</div>
    @endif
@endsection
