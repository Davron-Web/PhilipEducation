@extends('layouts.admin')

@section('title', 'Редактировать коллекцию')

@section('content')
    <x-admin.page-header :title="'Редактировать коллекцию: ' . $collection->title_ru" :backRoute="route('admin.vocabulary.collections.index')">
        <x-slot:actions>
            <a href="{{ route('admin.vocabulary.collections.show', $collection) }}" class="btn btn-ghost">Слова коллекции</a>
        </x-slot:actions>
    </x-admin.page-header>

    <form action="{{ route('admin.vocabulary.collections.update', $collection) }}" method="POST" class="card">
        @csrf
        @method('PUT')
        <div class="form-grid">
            <div class="form-group">
                <label class="form-label">Тип *</label>
                <select name="type" class="form-select" required>
                    <option value="level" {{ old('type', $collection->type) === 'level' ? 'selected' : '' }}>Уровень (level)</option>
                    <option value="topic" {{ old('type', $collection->type) === 'topic' ? 'selected' : '' }}>Тема (topic)</option>
                    <option value="exam" {{ old('type', $collection->type) === 'exam' ? 'selected' : '' }}>Экзамен (exam)</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Slug *</label>
                <input type="text" name="slug" class="form-input" value="{{ old('slug', $collection->slug) }}" required>
            </div>

            <div class="form-group">
                <label class="form-label">Название (RU) *</label>
                <input type="text" name="title_ru" class="form-input" value="{{ old('title_ru', $collection->title_ru) }}" required>
            </div>
            <div class="form-group">
                <label class="form-label">Название (EN)</label>
                <input type="text" name="title_en" class="form-input" value="{{ old('title_en', $collection->title_en) }}">
            </div>
            <div class="form-group">
                <label class="form-label">Название (TG)</label>
                <input type="text" name="title_tg" class="form-input" value="{{ old('title_tg', $collection->title_tg) }}">
            </div>
            <div class="form-group">
                <label class="form-label">Порядок сортировки</label>
                <input type="number" name="sort_order" class="form-input" value="{{ old('sort_order', $collection->sort_order) }}">
            </div>

            <div class="form-group full">
                <label class="form-label">Описание</label>
                <textarea name="description" class="form-input" rows="3">{{ old('description', $collection->description) }}</textarea>
            </div>

            <div class="form-group">
                <label class="form-label" style="display:flex;align-items:center;gap:8px">
                    <input type="checkbox" name="is_published" value="1" {{ old('is_published', $collection->is_published) ? 'checked' : '' }}>
                    Опубликована
                </label>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                Сохранить изменения
            </button>
            <a href="{{ route('admin.vocabulary.collections.index') }}" class="btn btn-ghost">Отмена</a>
        </div>
    </form>
@endsection
