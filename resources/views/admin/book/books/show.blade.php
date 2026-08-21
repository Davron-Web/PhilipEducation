@extends('layouts.admin')

@section('title', $book->title)

@section('content')
    <x-admin.page-header :title="$book->title" :backRoute="route('admin.book.books.index')">
        <x-slot:badge>
            @if($book->is_published)
                <span class="badge pub">Опубликовано</span>
            @else
                <span class="badge draft">Черновик</span>
            @endif
        </x-slot:badge>
        <x-slot:actions>
            <a href="{{ route('admin.book.books.edit', $book) }}" class="btn btn-warning">Изменить</a>
        </x-slot:actions>
    </x-admin.page-header>

    <div class="show-grid">
        <div class="card icon-card">
            @if($book->cover_image)
                <img src="{{ $book->cover_image }}" alt="{{ $book->title }}" class="media-preview">
            @else
                <div class="media-preview-empty" style="font-size:40px;font-weight:800;color:var(--deep)">
                    {{ mb_substr($book->title, 0, 1) }}
                </div>
            @endif
            <div>
                <div class="icon-card-title">{{ $book->title }}</div>
                <div style="color:var(--muted);font-weight:600;margin-top:4px">{{ $book->author }}</div>
            </div>
            @if($book->level)
                <span class="badge badge-primary">{{ $book->level->name }}</span>
            @endif
        </div>

        <div class="card">
            <h3>Информация</h3>
            <div class="info-row"><span class="info-label">ID</span><span class="info-value">#{{ $book->id }}</span></div>
            <div class="info-row"><span class="info-label">Автор</span><span class="info-value">{{ $book->author }}</span></div>
            <div class="info-row"><span class="info-label">Уровень</span><span class="info-value">{{ $book->level?->name ?? '—' }}</span></div>
            <div class="info-row"><span class="info-label">Страниц</span><span class="info-value">{{ $book->pages->count() }}</span></div>
            @if($book->description)
                <div class="info-row"><span class="info-label">Описание</span><span class="info-value">{{ $book->description }}</span></div>
            @endif
            <div class="info-row"><span class="info-label">Создано</span><span class="info-value">{{ $book->created_at?->format('d.m.Y H:i') }}</span></div>
            <div class="info-row"><span class="info-label">Обновлено</span><span class="info-value">{{ $book->updated_at?->format('d.m.Y H:i') }}</span></div>

            <div class="form-actions" style="margin-top:16px">
                <form action="{{ route('admin.book.books.destroy', $book) }}" method="POST" onsubmit="return confirm('Удалить книгу?');">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-danger">Удалить книгу</button>
                </form>
            </div>
        </div>
    </div>

    <div class="card table-card">
        <div class="table-head"><h3>Страницы <small>{{ $book->pages->count() }}</small></h3></div>
        @forelse($book->pages as $page)
            <div class="richtext-card" style="margin-top:14px">
                <div class="richtext-head">
                    <h3>Стр. {{ $page->page_number }}@if($page->title) — {{ $page->title }}@endif</h3>
                </div>
                <div class="richtext-body">{!! nl2br(e($page->content)) !!}</div>
            </div>
        @empty
            <div class="empty-row" style="margin-top:14px">Страницы пока не добавлены.</div>
        @endforelse
    </div>
@endsection
