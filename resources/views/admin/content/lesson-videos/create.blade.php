@extends('layouts.admin')

@section('title', 'Добавить видео')

@section('content')
    <x-admin.page-header title="Добавить видео" :backRoute="route('admin.content.lessonvideos.index')" />

    <form action="{{ route('admin.content.lessonvideos.store') }}" method="POST" class="card">
        @csrf
        <div class="form-grid">
            <div class="form-group full">
                <label class="form-label">Урок *</label>
                <select name="lesson_id" class="select @error('lesson_id') is-invalid @enderror" required>
                    <option value="">— выберите урок —</option>
                    @foreach($lessons as $id => $lessonTitle)
                        <option value="{{ $id }}" {{ (string) old('lesson_id', $video->lesson_id ?? $selectedLesson ?? '') === (string) $id ? 'selected' : '' }}>{{ $lessonTitle }}</option>
                    @endforeach
                </select>
                @error('lesson_id')<div class="error-text">{{ $message }}</div>@enderror
            </div>
            <div class="form-group full">
                <label class="form-label">Название *</label>
                <input type="text" name="title" class="form-input @error('title') is-invalid @enderror" value="{{ old('title', $video->title ?? '') }}" required>
                @error('title')<div class="error-text">{{ $message }}</div>@enderror
            </div>
            <div class="form-group full">
                <label class="form-label">Ссылка на видео *</label>
                <input type="url" name="url" class="form-input @error('url') is-invalid @enderror" value="{{ old('url', $video->url ?? '') }}" placeholder="https://www.youtube.com/watch?v=..." required>
                @error('url')<div class="error-text">{{ $message }}</div>@enderror
                <div class="form-hint">YouTube или Vimeo — плеер встроится в страницу урока. Другие ссылки откроются в новой вкладке.</div>
            </div>
            <div class="form-group full">
                <label class="form-label">Описание</label>
                <input type="text" name="description" class="form-input" value="{{ old('description', $video->description ?? '') }}" maxlength="255">
            </div>
            <div class="form-group">
                <label class="form-label">Длительность, мин</label>
                <input type="number" name="duration_minutes" class="form-input" value="{{ old('duration_minutes', $video->duration_minutes ?? '') }}" min="1" max="600">
            </div>
            <div class="form-group">
                <label class="form-label">Порядок</label>
                <input type="number" name="sort_order" class="form-input" value="{{ old('sort_order', $video->sort_order ?? 0) }}" min="0" max="999">
                <div class="form-hint">Меньше — выше в списке.</div>
            </div>
            <div class="form-group full">
                <label class="form-label">
                    <input type="checkbox" name="is_published" value="1" {{ old('is_published', $video->is_published ?? true) ? 'checked' : '' }}>
                    Показывать ученикам
                </label>
            </div>
        </div>
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Добавить</button>
            <a href="{{ route('admin.content.lessonvideos.index') }}" class="btn btn-ghost">Отмена</a>
        </div>
    </form>
@endsection