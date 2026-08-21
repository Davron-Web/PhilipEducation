@extends('layouts.admin')

@section('title', 'Статистика обучения')

@section('content')
    <x-admin.page-header title="Статистика обучения">
        <x-slot:actions>
            <a href="{{ route('admin.gamification.studystatistics.create') }}" class="btn btn-primary">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Добавить статистику
            </a>
        </x-slot:actions>
    </x-admin.page-header>

    <div class="card">
        <form method="GET" class="filters">
            <select name="user_id" class="select">
                <option value="">Все пользователи</option>
                @foreach($users as $user)
                    <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                @endforeach
            </select>
            <button type="submit" class="btn btn-ghost">Фильтр</button>
            <a href="{{ route('admin.gamification.studystatistics.index') }}" class="btn btn-ghost">Сброс</a>
        </form>
    </div>

    <div class="card table-card">
        <div class="table-scroll">
            <table class="tbl">
                <thead>
                <tr>
                    <th style="width:70px">ID</th>
                    <th>Пользователь</th>
                    <th>Уроков пройдено</th>
                    <th>Тестов сдано</th>
                    <th>Слов изучено</th>
                    <th>Время (мин)</th>
                    <th style="width:220px;text-align:right">Действия</th>
                </tr>
                </thead>
                <tbody>
                @forelse($studyStatistics as $stat)
                    <tr>
                        <td class="id-cell">#{{ $stat->id }}</td>
                        <td class="title-cell">{{ $stat->user?->name ?? '—' }}</td>
                        <td>{{ $stat->total_lessons_completed }}</td>
                        <td>{{ $stat->total_tests_passed }}</td>
                        <td>{{ $stat->total_words_learned }}</td>
                        <td>{{ $stat->study_time_minutes }}</td>
                        <td>
                            <div class="actions">
                                <a href="{{ route('admin.gamification.studystatistics.show', $stat) }}" class="btn btn-sm btn-ghost">Просмотр</a>
                                <a href="{{ route('admin.gamification.studystatistics.edit', $stat) }}" class="btn btn-sm btn-warning">Изменить</a>
                                <form action="{{ route('admin.gamification.studystatistics.destroy', $stat) }}" method="POST" onsubmit="return confirm('Удалить статистику?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">Удалить</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr class="empty-row"><td colspan="7">Статистика не найдена</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if(method_exists($studyStatistics, 'hasPages') && $studyStatistics->hasPages())
        <div class="pagination">{{ $studyStatistics->withQueryString()->links() }}</div>
    @endif
@endsection
