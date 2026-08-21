@extends('layouts.admin')

@section('title', 'Результаты пользователей')

@section('content')
    <x-admin.page-header title="Результаты пользователей">
        <x-slot:actions>
            <a href="{{ route('admin.user.userresults.create') }}" class="btn btn-primary">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Добавить результат
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
            <select name="test_id" class="select">
                <option value="">Все тесты</option>
                @foreach($tests as $id => $title)
                    <option value="{{ $id }}" {{ request('test_id') == $id ? 'selected' : '' }}>{{ $title }}</option>
                @endforeach
            </select>
            <select name="status" class="select">
                <option value="">Все результаты</option>
                <option value="passed" {{ request('status') == 'passed' ? 'selected' : '' }}>Сдано</option>
                <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Не сдано</option>
            </select>
            <button type="submit" class="btn btn-ghost">Фильтр</button>
            <a href="{{ route('admin.user.userresults.index') }}" class="btn btn-ghost">Сброс</a>
        </form>
    </div>

    <div class="card table-card">
        <div class="table-scroll">
            <table class="tbl">
                <thead>
                <tr>
                    <th style="width:70px">ID</th>
                    <th>Пользователь</th>
                    <th>Тест</th>
                    <th>Балл</th>
                    <th>Результат</th>
                    <th>Дата попытки</th>
                    <th style="width:220px;text-align:right">Действия</th>
                </tr>
                </thead>
                <tbody>
                @forelse($results as $result)
                    <tr>
                        <td class="id-cell">#{{ $result->id }}</td>
                        <td>
                            @if($result->user)
                                <a class="chip-link" href="{{ route('admin.user.users.show', $result->user) }}">{{ $result->user->name ?? $result->user->email }}</a>
                            @else
                                <span class="id-cell">—</span>
                            @endif
                        </td>
                        <td>
                            @if($result->test)
                                <a class="chip-link" href="{{ route('admin.test.tests.show', $result->test) }}">{{ Str::limit($result->test->title, 30) }}</a>
                            @else
                                <span class="id-cell">—</span>
                            @endif
                        </td>
                        <td>
                            <div class="meter">
                                <div class="meter-track"><span class="meter-fill" style="width: {{ min($result->score, 100) }}%"></span></div>
                                <span>{{ $result->score }}%</span>
                            </div>
                        </td>
                        <td>
                            @if($result->passed)
                                <span class="badge badge-success">✓ Сдано</span>
                            @else
                                <span class="badge badge-danger">Не сдано</span>
                            @endif
                        </td>
                        <td>{{ $result->attempt_date?->format('d.m.Y H:i') }}</td>
                        <td>
                            <div class="actions">
                                <a href="{{ route('admin.user.userresults.show', $result) }}" class="btn btn-sm btn-ghost">Просмотр</a>
                                <a href="{{ route('admin.user.userresults.edit', $result) }}" class="btn btn-sm btn-warning">Изменить</a>
                                <form action="{{ route('admin.user.userresults.destroy', $result) }}" method="POST" onsubmit="return confirm('Удалить результат?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">Удалить</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr class="empty-row"><td colspan="7">Результаты не найдены</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if(method_exists($results, 'hasPages') && $results->hasPages())
        <div class="pagination">{{ $results->withQueryString()->links() }}</div>
    @endif
@endsection
