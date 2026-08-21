@extends('layouts.admin')

@section('title', 'Слова пользователей')

@section('content')
    <x-admin.page-header title="Слова пользователей">
        <x-slot:actions>
            <a href="{{ route('admin.user.userwords.create') }}" class="btn btn-primary">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Добавить запись
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
            <select name="word_id" class="select">
                <option value="">Все слова</option>
                @foreach($words as $id => $word)
                    <option value="{{ $id }}" {{ request('word_id') == $id ? 'selected' : '' }}>{{ $word }}</option>
                @endforeach
            </select>
            <select name="learned" class="select">
                <option value="">Любой статус</option>
                <option value="1" {{ request('learned') === '1' ? 'selected' : '' }}>Выучено</option>
                <option value="0" {{ request('learned') === '0' ? 'selected' : '' }}>В процессе</option>
            </select>
            <button type="submit" class="btn btn-ghost">Фильтр</button>
            <a href="{{ route('admin.user.userwords.index') }}" class="btn btn-ghost">Сброс</a>
        </form>
    </div>

    <div class="card table-card">
        <div class="table-scroll">
            <table class="tbl">
                <thead>
                <tr>
                    <th style="width:60px">ID</th>
                    <th>Пользователь</th>
                    <th>Слово</th>
                    <th>Статус</th>
                    <th>Точность</th>
                    <th>Последний повтор</th>
                    <th style="width:220px;text-align:right">Действия</th>
                </tr>
                </thead>
                <tbody>
                @forelse($userWords as $userWord)
                    @php
                        $total = $userWord->correct_answers + $userWord->wrong_answers;
                        $accuracy = $total > 0 ? round(($userWord->correct_answers / $total) * 100, 1) : 0;
                        $accColor = $accuracy >= 80 ? 'var(--green)' : ($accuracy >= 50 ? '#A16207' : 'var(--red)');
                    @endphp
                    <tr>
                        <td class="id-cell">#{{ $userWord->id }}</td>
                        <td>
                            @if($userWord->user)
                                <a class="chip-link" href="{{ route('admin.user.users.show', $userWord->user) }}">{{ $userWord->user->name ?? $userWord->user->email }}</a>
                            @else
                                <span class="id-cell">—</span>
                            @endif
                        </td>
                        <td>
                            @if($userWord->word)
                                <a class="chip-link" href="{{ route('admin.vocabulary.words.show', $userWord->word) }}">{{ $userWord->word->word }}</a>
                            @else
                                <span class="id-cell">—</span>
                            @endif
                        </td>
                        <td>
                            @if($userWord->learned)
                                <span class="badge badge-success">Выучено</span>
                            @else
                                <span class="badge badge-warning">В процессе</span>
                            @endif
                        </td>
                        <td>
                            <div class="meter">
                                <div class="meter-track" style="width:70px"><span class="meter-fill" style="width:{{ $accuracy }}%;background:{{ $accColor }}"></span></div>
                                <span class="id-cell">{{ $accuracy }}%</span>
                            </div>
                        </td>
                        <td class="id-cell">{{ optional($userWord->last_reviewed_at)->format('d.m.Y H:i') ?? 'Никогда' }}</td>
                        <td>
                            <div class="actions">
                                <a href="{{ route('admin.user.userwords.show', $userWord) }}" class="btn btn-sm btn-ghost">Просмотр</a>
                                <a href="{{ route('admin.user.userwords.edit', $userWord) }}" class="btn btn-sm btn-warning">Изменить</a>
                                <form action="{{ route('admin.user.userwords.destroy', $userWord) }}" method="POST" onsubmit="return confirm('Удалить запись?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">Удалить</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr class="empty-row"><td colspan="7">Записи не найдены</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if(method_exists($userWords, 'hasPages') && $userWords->hasPages())
        <div class="pagination">{{ $userWords->withQueryString()->links() }}</div>
    @endif
@endsection
