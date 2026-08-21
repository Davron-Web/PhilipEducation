@extends('layouts.admin')

@section('title', 'Тестовые вопросы')

@section('content')
    <x-admin.page-header title="Тестовые вопросы">
        <x-slot:actions>
            <a href="{{ route('admin.test.testquestions.create') }}" class="btn btn-primary">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Добавить вопрос
            </a>
        </x-slot:actions>
    </x-admin.page-header>

    <div class="card">
        <form method="GET" class="filters">
            <input type="text" name="search" class="input" placeholder="Поиск по вопросу..." value="{{ request('search') }}">
            <select name="test_id" class="select">
                <option value="">Все тесты</option>
                @foreach($tests as $id => $title)
                    <option value="{{ $id }}" {{ request('test_id') == $id ? 'selected' : '' }}>{{ $title }}</option>
                @endforeach
            </select>
            <select name="type" class="select">
                <option value="">Все типы</option>
                @foreach($types as $type => $label)
                    <option value="{{ $type }}" {{ request('type') == $type ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
            <button type="submit" class="btn btn-ghost">Фильтр</button>
            <a href="{{ route('admin.test.testquestions.index') }}" class="btn btn-ghost">Сброс</a>
        </form>
    </div>

    <div class="card table-card">
        <div class="table-scroll">
            <table class="tbl">
                <thead>
                <tr>
                    <th style="width:70px">ID</th>
                    <th>Тест</th>
                    <th>Вопрос</th>
                    <th style="width:150px">Тип</th>
                    <th style="width:90px">Баллы</th>
                    <th style="width:220px;text-align:right">Действия</th>
                </tr>
                </thead>
                <tbody>
                @forelse($questions as $question)
                    <tr>
                        <td class="id-cell">#{{ $question->id }}</td>
                        <td>
                            @if($question->test)
                                <a class="chip-link" href="{{ route('admin.test.tests.show', $question->test) }}">{{ \Illuminate\Support\Str::limit($question->test->title, 30) }}</a>
                            @else
                                <span class="id-cell">—</span>
                            @endif
                        </td>
                        <td class="text-cell">{{ \Illuminate\Support\Str::limit($question->question, 60) }}</td>
                        <td><span class="badge badge-{{ $question->type_badge_color }}">{{ $question->type_label }}</span></td>
                        <td><span class="badge badge-primary">{{ $question->points }}</span></td>
                        <td>
                            <div class="actions">
                                <a href="{{ route('admin.test.testquestions.show', $question) }}" class="btn btn-sm btn-ghost">Просмотр</a>
                                <a href="{{ route('admin.test.testquestions.edit', $question) }}" class="btn btn-sm btn-warning">Изменить</a>
                                <form action="{{ route('admin.test.testquestions.destroy', $question) }}" method="POST" onsubmit="return confirm('Удалить вопрос?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">Удалить</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr class="empty-row"><td colspan="6">Вопросы не найдены</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if(method_exists($questions, 'hasPages') && $questions->hasPages())
        <div class="pagination">{{ $questions->withQueryString()->links() }}</div>
    @endif
@endsection
