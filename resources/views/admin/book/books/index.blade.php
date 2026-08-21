@extends('layouts.admin')

@section('title', 'Книги')

@section('content')
    <x-admin.page-header title="Книги">
        <x-slot:actions>
            <a href="{{ route('admin.book.books.create') }}" class="btn btn-primary">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Добавить книгу
            </a>
        </x-slot:actions>
    </x-admin.page-header>

    <div class="card">
        <form method="GET" class="filters">
            <input type="text" name="search" class="input" placeholder="Поиск по названию или автору..." value="{{ request('search') }}">
            <select name="level_id" class="select">
                <option value="">Все уровни</option>
                @foreach($levels as $level)
                    <option value="{{ $level->id }}" {{ request('level_id') == $level->id ? 'selected' : '' }}>{{ $level->name }}</option>
                @endforeach
            </select>
            <select name="is_published" class="select">
                <option value="">Все статусы</option>
                <option value="1" {{ request('is_published') === '1' ? 'selected' : '' }}>Опубликовано</option>
                <option value="0" {{ request('is_published') === '0' ? 'selected' : '' }}>Черновик</option>
            </select>
            <button type="submit" class="btn btn-ghost">Фильтр</button>
            <a href="{{ route('admin.book.books.index') }}" class="btn btn-ghost">Сброс</a>
        </form>
    </div>

    <div class="card table-card">
        <div class="table-scroll">
            <table class="tbl">
                <thead>
                <tr>
                    <th style="width:60px"></th>
                    <th>Название</th>
                    <th>Автор</th>
                    <th style="width:100px">Уровень</th>
                    <th style="width:90px">Страниц</th>
                    <th style="width:120px">Статус</th>
                    <th style="width:220px;text-align:right">Действия</th>
                </tr>
                </thead>
                <tbody>
                @forelse($books as $book)
                    <tr>
                        <td>
                            @if($book->cover_image)
                                <img src="{{ $book->cover_image }}" alt="{{ $book->title }}" class="media-thumb" style="width:36px;height:48px;border-radius:6px;">
                            @else
                                <div class="media-thumb-empty" style="width:36px;height:48px;border-radius:6px;font-size:13px;font-weight:800;color:var(--deep)">
                                    {{ mb_substr($book->title, 0, 1) }}
                                </div>
                            @endif
                        </td>
                        <td class="title-cell">{{ $book->title }}</td>
                        <td class="text-cell">{{ $book->author }}</td>
                        <td>{{ $book->level?->code ?? '—' }}</td>
                        <td>{{ $book->pages->count() }}</td>
                        <td>
                            @if($book->is_published)
                                <span class="badge pub">Опубликовано</span>
                            @else
                                <span class="badge draft">Черновик</span>
                            @endif
                        </td>
                        <td>
                            <div class="actions">
                                <a href="{{ route('admin.book.books.show', $book) }}" class="btn btn-sm btn-ghost">Просмотр</a>
                                <a href="{{ route('admin.book.books.edit', $book) }}" class="btn btn-sm btn-warning">Изменить</a>
                                <form action="{{ route('admin.book.books.destroy', $book) }}" method="POST" onsubmit="return confirm('Удалить книгу «{{ addslashes($book->title) }}»?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">Удалить</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr class="empty-row"><td colspan="7">Книги не найдены</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if(method_exists($books, 'hasPages') && $books->hasPages())
        <div class="pagination">{{ $books->withQueryString()->links() }}</div>
    @endif
@endsection
