@extends('layouts.admin')

@section('title', 'Ответы тестов')

@section('content')
    <x-admin.page-header title="Ответы тестов">
        <x-slot:actions>
            <a href="{{ route('admin.test.testanswers.create') }}" class="btn btn-primary">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Добавить ответ
            </a>
        </x-slot:actions>
    </x-admin.page-header>

    <div class="card">
        <form method="GET" class="filters">
            <input type="text" name="search" class="input" placeholder="Поиск по ответу..." value="{{ request('search') }}">
            <select name="question_id" class="select">
                <option value="">Все вопросы</option>
                @foreach($questions as $question)
                    <option value="{{ $question->id }}" {{ request('question_id') == $question->id ? 'selected' : '' }}>{{ \Illuminate\Support\Str::limit($question->question, 40) }}</option>
                @endforeach
            </select>
            <select name="is_correct" class="select">
                <option value="">Все ответы</option>
                <option value="1" {{ request('is_correct') === '1' ? 'selected' : '' }}>Только правильные</option>
                <option value="0" {{ request('is_correct') === '0' ? 'selected' : '' }}>Только неправильные</option>
            </select>
            <button type="submit" class="btn btn-ghost">Фильтр</button>
            <a href="{{ route('admin.test.testanswers.index') }}" class="btn btn-ghost">Сброс</a>
        </form>
    </div>

    <div class="card table-card">
        <div class="table-scroll">
            <table class="tbl">
                <thead>
                <tr>
                    <th style="width:70px">ID</th>
                    <th>Вопрос</th>
                    <th>Ответ</th>
                    <th style="width:130px">Правильный</th>
                    <th style="width:220px;text-align:right">Действия</th>
                </tr>
                </thead>
                <tbody>
                @forelse($answers as $answer)
                    <tr>
                        <td class="id-cell">#{{ $answer->id }}</td>
                        <td>
                            @if($answer->question)
                                <a class="chip-link" href="{{ route('admin.test.testquestions.show', $answer->question) }}">{{ \Illuminate\Support\Str::limit($answer->question->question, 40) }}</a>
                            @else
                                <span class="id-cell">—</span>
                            @endif
                        </td>
                        <td class="text-cell">{{ $answer->answer }}</td>
                        <td>
                            @if($answer->is_correct)
                                <span class="badge badge-success">✓ Верно</span>
                            @else
                                <span class="badge badge-secondary">Неверно</span>
                            @endif
                        </td>
                        <td>
                            <div class="actions">
                                <a href="{{ route('admin.test.testanswers.show', $answer) }}" class="btn btn-sm btn-ghost">Просмотр</a>
                                <a href="{{ route('admin.test.testanswers.edit', $answer) }}" class="btn btn-sm btn-warning">Изменить</a>
                                <form action="{{ route('admin.test.testanswers.destroy', $answer) }}" method="POST" onsubmit="return confirm('Удалить ответ?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">Удалить</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr class="empty-row"><td colspan="5">Ответы не найдены</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if(method_exists($answers, 'hasPages') && $answers->hasPages())
        <div class="pagination">{{ $answers->withQueryString()->links() }}</div>
    @endif
@endsection
