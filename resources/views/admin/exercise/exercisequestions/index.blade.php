@extends('layouts.admin')

@section('title', 'Вопросы упражнений')

@section('content')
    <x-admin.page-header title="Вопросы упражнений">
        <x-slot:actions>
            <a href="{{ route('admin.exercise.exercisequestions.create') }}" class="btn btn-primary">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Добавить вопрос
            </a>
        </x-slot:actions>
    </x-admin.page-header>

    <div class="card">
        <form method="GET" class="filters">
            <input type="text" name="search" class="input" placeholder="Поиск по вопросу или ответу..." value="{{ request('search') }}">
            <select name="exercise_id" class="select">
                <option value="">Все упражнения</option>
                @foreach($exercises as $exercise)
                    <option value="{{ $exercise->id }}" {{ request('exercise_id') == $exercise->id ? 'selected' : '' }}>{{ $exercise->title }}</option>
                @endforeach
            </select>
            <button type="submit" class="btn btn-ghost">Фильтр</button>
            <a href="{{ route('admin.exercise.exercisequestions.index') }}" class="btn btn-ghost">Сброс</a>
        </form>
    </div>

    <div class="card table-card">
        <div class="table-scroll">
            <table class="tbl">
                <thead>
                <tr>
                    <th style="width:70px">ID</th>
                    <th>Упражнение</th>
                    <th>Вопрос</th>
                    <th>Правильный ответ</th>
                    <th style="width:220px;text-align:right">Действия</th>
                </tr>
                </thead>
                <tbody>
                @forelse($questions as $question)
                    <tr>
                        <td class="id-cell">#{{ $question->id }}</td>
                        <td>
                            @if($question->exercise)
                                <a class="chip-link" href="{{ route('admin.exercise.exercises.show', $question->exercise) }}">{{ \Illuminate\Support\Str::limit($question->exercise->title, 30) }}</a>
                            @else
                                <span class="id-cell">—</span>
                            @endif
                        </td>
                        <td class="text-cell">{{ \Illuminate\Support\Str::limit($question->question, 60) }}</td>
                        <td class="id-cell">{{ \Illuminate\Support\Str::limit($question->correct_answer, 40) }}</td>
                        <td>
                            <div class="actions">
                                <a href="{{ route('admin.exercise.exercisequestions.show', $question) }}" class="btn btn-sm btn-ghost">Просмотр</a>
                                <a href="{{ route('admin.exercise.exercisequestions.edit', $question) }}" class="btn btn-sm btn-warning">Изменить</a>
                                <form action="{{ route('admin.exercise.exercisequestions.destroy', $question) }}" method="POST" onsubmit="return confirm('Удалить вопрос?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">Удалить</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr class="empty-row"><td colspan="5">Вопросы не найдены</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if(method_exists($questions, 'hasPages') && $questions->hasPages())
        <div class="pagination">{{ $questions->withQueryString()->links() }}</div>
    @endif
@endsection
