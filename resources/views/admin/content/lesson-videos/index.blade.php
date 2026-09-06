@extends('layouts.admin')

@section('title', 'Видеоуроки')

@section('content')
    <x-admin.page-header title="Видеоуроки">
        <x-slot:actions>
            <a href="{{ route('admin.content.lessonvideos.create', ['lesson_id' => request('lesson_id')]) }}" class="btn btn-primary">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Добавить видео
            </a>
        </x-slot:actions>
    </x-admin.page-header>

    <div class="card">
        <form method="GET" class="filters">
            <input type="text" name="search" class="input" placeholder="Поиск по названию..." value="{{ request('search') }}">
            <select name="lesson_id" class="select">
                <option value="">Все уроки</option>
                @foreach($lessons as $id => $title)
                    <option value="{{ $id }}" {{ request('lesson_id') == $id ? 'selected' : '' }}>{{ $title }}</option>
                @endforeach
            </select>
            <button type="submit" class="btn btn-ghost">Фильтр</button>
            <a href="{{ route('admin.content.lessonvideos.index') }}" class="btn btn-ghost">Сброс</a>
        </form>
    </div>

    <div class="card table-card">
        <div class="table-scroll">
            <table class="tbl">
                <thead>
                <tr>
                    <th style="width:70px">ID</th>
                    <th>Название</th>
                    <th>Урок</th>
                    <th style="width:130px">Плеер</th>
                    <th style="width:90px">Порядок</th>
                    <th style="width:100px">Статус</th>
                    <th style="width:200px;text-align:right">Действия</th>
                </tr>
                </thead>
                <tbody>
                @forelse($videos as $video)
                    <tr>
                        <td class="id-cell">#{{ $video->id }}</td>
                        <td class="title-cell">
                            {{ $video->title }}
                            <div style="opacity:.6;font-size:12px;word-break:break-all">{{ Str::limit($video->url, 60) }}</div>
                        </td>
                        <td class="text-cell">{{ $video->lesson?->title ?? '—' }}</td>
                        <td>
                            @if($video->embedUrl())
                                <span class="badge badge-success">встроится</span>
                            @else
                                <span class="badge badge-warning">только ссылка</span>
                            @endif
                        </td>
                        <td>{{ $video->sort_order }}</td>
                        <td>
                            <span class="badge badge-{{ $video->is_published ? 'success' : 'warning' }}">
                                {{ $video->is_published ? 'Показан' : 'Скрыт' }}
                            </span>
                        </td>
                        <td>
                            <div class="actions">
                                <a href="{{ route('admin.content.lessonvideos.edit', $video) }}" class="btn btn-sm btn-warning">Изменить</a>
                                <form action="{{ route('admin.content.lessonvideos.destroy', $video) }}" method="POST" onsubmit="return confirm('Удалить видео?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">Удалить</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr class="empty-row"><td colspan="7">Видео пока не добавлены</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($videos->hasPages())
        <div class="pagination">{{ $videos->withQueryString()->links() }}</div>
    @endif
@endsection
