@extends('layouts.admin')

@section('title', 'Блок контента #' . $lessonContent->id)

@section('content')
    <x-admin.page-header :title="'Блок контента #' . $lessonContent->id" :backRoute="route('admin.content.lessoncontents.index')">
        <x-slot:badge>
            <span class="badge badge-primary">{{ $lessonContent->type }}</span>
        </x-slot:badge>
        <x-slot:actions>
            <a href="{{ route('admin.content.lessoncontents.edit', $lessonContent) }}" class="btn btn-warning">Изменить</a>
        </x-slot:actions>
    </x-admin.page-header>

    <div class="card">
        <h3>Информация</h3>
        <div class="info-row"><span class="info-label">ID</span><span class="info-value">#{{ $lessonContent->id }}</span></div>
        <div class="info-row">
            <span class="info-label">Урок</span>
            <span class="info-value">
                @if($lessonContent->lesson)
                    <a class="chip-link" href="{{ route('admin.content.lessons.show', $lessonContent->lesson) }}">{{ $lessonContent->lesson->title }}</a>
                @else
                    —
                @endif
            </span>
        </div>
        <div class="info-row"><span class="info-label">Название</span><span class="info-value">{{ $lessonContent->title ?? '—' }}</span></div>
        <div class="info-row"><span class="info-label">Порядок</span><span class="info-value">{{ $lessonContent->order_number }}</span></div>
        @if($lessonContent->file_url)
            <div class="info-row"><span class="info-label">Файл</span><span class="info-value"><a class="chip-link" href="{{ $lessonContent->file_url }}" target="_blank">{{ $lessonContent->file_url }}</a></span></div>
        @endif
        <div class="info-row"><span class="info-label">Создано</span><span class="info-value">{{ $lessonContent->created_at?->format('d.m.Y H:i') }}</span></div>
        <div class="info-row"><span class="info-label">Обновлено</span><span class="info-value">{{ $lessonContent->updated_at?->format('d.m.Y H:i') }}</span></div>
    </div>

    @if($lessonContent->content)
        <div class="card richtext-card">
            <div class="richtext-head"><h3>Содержимое</h3></div>
            <div class="richtext-body">{!! nl2br(e($lessonContent->content)) !!}</div>
        </div>
    @endif

    <div class="form-actions">
        <form action="{{ route('admin.content.lessoncontents.destroy', $lessonContent) }}" method="POST" onsubmit="return confirm('Удалить блок контента?');">
            @csrf @method('DELETE')
            <button type="submit" class="btn btn-danger">Удалить блок</button>
        </form>
    </div>
@endsection
