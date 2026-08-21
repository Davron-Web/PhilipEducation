@extends('layouts.admin')

@section('title', 'Добавить блок контента')

@section('content')
    <x-admin.page-header title="Добавить блок контента" :backRoute="route('admin.content.lessoncontents.index')" />

    <form action="{{ route('admin.content.lessoncontents.store') }}" method="POST" class="card">
        @csrf
        <div class="form-grid">
            <div class="form-group">
                <label class="form-label">Урок *</label>
                <select name="lesson_id" class="form-select @error('lesson_id') is-invalid @enderror" required>
                    <option value="">Выберите урок</option>
                    @foreach($lessons as $lesson)
                        <option value="{{ $lesson->id }}" {{ old('lesson_id', request('lesson_id')) == $lesson->id ? 'selected' : '' }}>{{ $lesson->title }}</option>
                    @endforeach
                </select>
                @error('lesson_id')<div class="error-text">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label class="form-label">Тип *</label>
                <select name="type" class="form-select @error('type') is-invalid @enderror" required>
                    <option value="text" {{ old('type') == 'text' ? 'selected' : '' }}>Текст</option>
                    <option value="video" {{ old('type') == 'video' ? 'selected' : '' }}>Видео</option>
                    <option value="audio" {{ old('type') == 'audio' ? 'selected' : '' }}>Аудио</option>
                    <option value="image" {{ old('type') == 'image' ? 'selected' : '' }}>Изображение</option>
                    <option value="exercise" {{ old('type') == 'exercise' ? 'selected' : '' }}>Упражнение</option>
                </select>
                @error('type')<div class="error-text">{{ $message }}</div>@enderror
            </div>
            <div class="form-group full">
                <label class="form-label">Название</label>
                <input type="text" name="title" class="form-input @error('title') is-invalid @enderror" value="{{ old('title') }}">
                @error('title')<div class="error-text">{{ $message }}</div>@enderror
            </div>
            <div class="form-group full">
                <label class="form-label">Содержимое</label>
                <textarea name="content" class="form-textarea" rows="6">{{ old('content') }}</textarea>
            </div>
            <div class="form-group">
                <label class="form-label">Ссылка на файл</label>
                <input type="text" name="file_url" class="form-input @error('file_url') is-invalid @enderror" value="{{ old('file_url') }}" placeholder="URL видео/аудио/изображения">
                @error('file_url')<div class="error-text">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label class="form-label">Порядок</label>
                <input type="number" name="order_number" class="form-input" value="{{ old('order_number') }}" min="0" placeholder="Авто, если не указано">
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Создать</button>
            <a href="{{ route('admin.content.lessoncontents.index') }}" class="btn btn-ghost">Отмена</a>
        </div>
    </form>
@endsection
