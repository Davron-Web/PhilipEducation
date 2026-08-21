@extends('layouts.admin')

@section('title', 'Роль: ' . $role->name)

@section('content')
    <x-admin.page-header :title="'Роль: ' . $role->name" :backRoute="route('admin.user.roles.index')">
        <x-slot:actions>
            <a href="{{ route('admin.user.roles.edit', $role) }}" class="btn btn-warning">Изменить</a>
        </x-slot:actions>
    </x-admin.page-header>

    <div class="card">
        <h3>Информация</h3>
        <div class="info-row"><span class="info-label">ID</span><span class="info-value">#{{ $role->id }}</span></div>
        <div class="info-row"><span class="info-label">Название</span><span class="info-value" style="font-weight:700">{{ $role->name }}</span></div>
        <div class="info-row"><span class="info-label">Описание</span><span class="info-value">{{ $role->description ?? '—' }}</span></div>
        <div class="info-row"><span class="info-label">Пользователей</span><span class="info-value">{{ $role->users->count() }}</span></div>
        <div class="info-row"><span class="info-label">Создано</span><span class="info-value">{{ $role->created_at }}</span></div>
        <div class="info-row"><span class="info-label">Обновлено</span><span class="info-value">{{ $role->updated_at }}</span></div>
    </div>

    <div class="card table-card">
        <div class="table-head"><h3>Пользователи с этой ролью <small>{{ $role->users->count() }}</small></h3></div>
        <div class="table-scroll">
            <table class="tbl">
                <thead><tr><th style="width:70px">ID</th><th>Имя</th><th>Email</th></tr></thead>
                <tbody>
                @forelse($role->users as $user)
                    <tr>
                        <td class="id-cell">#{{ $user->id }}</td>
                        <td class="title-cell">{{ $user->name }}</td>
                        <td class="text-cell">{{ $user->email }}</td>
                    </tr>
                @empty
                    <tr class="empty-row"><td colspan="3">Нет пользователей</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
