@extends('layouts.admin')

@section('title', 'Содержание урока')

@section('content')
    <x-admin.page-header title="Содержание урока">
        <x-slot:actions>
            <a href="{{ route('admin.content.lessoncontents.create') }}" class="btn btn-primary">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Добавить блок
            </a>
        </x-slot:actions>
    </x-admin.page-header>

    <div class="card">
        <form method="GET" class="filters">
            <select name="lesson_id" class="select">
                <option value="">Все уроки</option>
                @foreach($lessons as $id => $title)
                    <option value="{{ $id }}" {{ request('lesson_id') == $id ? 'selected' : '' }}>{{ $title }}</option>
                @endforeach
            </select>
            <select name="type" class="select">
                <option value="">Все типы</option>
                <option value="text" {{ request('type') == 'text' ? 'selected' : '' }}>Текст</option>
                <option value="video" {{ request('type') == 'video' ? 'selected' : '' }}>Видео</option>
                <option value="audio" {{ request('type') == 'audio' ? 'selected' : '' }}>Аудио</option>
                <option value="image" {{ request('type') == 'image' ? 'selected' : '' }}>Изображение</option>
                <option value="exercise" {{ request('type') == 'exercise' ? 'selected' : '' }}>Упражнение</option>
            </select>
            <button type="submit" class="btn btn-ghost">Фильтр</button>
            <a href="{{ route('admin.content.lessoncontents.index') }}" class="btn btn-ghost">Сброс</a>
        </form>
    </div>

    <div class="card table-card">
        <div class="table-scroll">
            <table class="tbl">
                <thead>
                <tr>
                    <th style="width:70px">ID</th>
                    <th>Урок</th>
                    <th style="width:120px">Тип</th>
                    <th>Название</th>
                    <th style="width:90px">Порядок</th>
                    <th style="width:220px;text-align:right">Действия</th>
                </tr>
                </thead>
                <tbody>
                @forelse($contents as $item)
                    <tr>
                        <td class="id-cell">#{{ $item->id }}</td>
                        <td>
                            @if($item->lesson)
                                <a class="chip-link" href="{{ route('admin.content.lessons.show', $item->lesson) }}">{{ Str::limit($item->lesson->title, 30) }}</a>
                            @else
                                <span class="id-cell">—</span>
                            @endif
                        </td>
                        <td><span class="badge badge-primary">{{ $item->type }}</span></td>
                        <td class="title-cell">{{ $item->title ?? '—' }}</td>
                        <td>{{ $item->order_number }}</td>
                        <td>
                            <div class="actions">
                                <a href="{{ route('admin.content.lessoncontents.show', $item) }}" class="btn btn-sm btn-ghost">Просмотр</a>
                                <a href="{{ route('admin.content.lessoncontents.edit', $item) }}" class="btn btn-sm btn-warning">Изменить</a>
                                <form action="{{ route('admin.content.lessoncontents.destroy', $item) }}" method="POST" onsubmit="return confirm('Удалить блок контента?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">Удалить</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr class="empty-row"><td colspan="6">Контент не найден</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if(method_exists($contents, 'hasPages') && $contents->hasPages())
        <div class="pagination">{{ $contents->withQueryString()->links() }}</div>
    @endif
@endsection
