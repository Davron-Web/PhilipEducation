@extends('layouts.admin')

@section('title', 'Добавить коллекцию')

@section('content')
    <x-admin.page-header title="Добавить коллекцию" :backRoute="route('admin.vocabulary.collections.index')" />

    <form action="{{ route('admin.vocabulary.collections.store') }}" method="POST" class="card">
        @csrf
        <div class="form-grid">
            <div class="form-group">
                <label class="form-label">Тип *</label>
                <select name="type" class="form-select" required>
                    <option value="level" {{ old('type') === 'level' ? 'selected' : '' }}>Уровень (level)</option>
                    <option value="topic" {{ old('type') === 'topic' ? 'selected' : '' }}>Тема (topic)</option>
                    <option value="exam" {{ old('type') === 'exam' ? 'selected' : '' }}>Экзамен (exam)</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Slug *</label>
                <input type="text" name="slug" class="form-input" placeholder="например: a1, food, ielts" value="{{ old('slug') }}" required>
            </div>

            <div class="form-group">
                <label class="form-label">Название (RU) *</label>
                <input type="text" name="title_ru" class="form-input" value="{{ old('title_ru') }}" required>
            </div>
            <div class="form-group">
                <label class="form-label">Название (EN)</label>
                <input type="text" name="title_en" class="form-input" value="{{ old('title_en') }}">
            </div>
            <div class="form-group">
                <label class="form-label">Название (TG)</label>
                <input type="text" name="title_tg" class="form-input" value="{{ old('title_tg') }}">
            </div>
            <div class="form-group">
                <label class="form-label">Порядок сортировки</label>
                <input type="number" name="sort_order" class="form-input" value="{{ old('sort_order', 0) }}">
            </div>

            <div class="form-group full">
                <label class="form-label">Описание</label>
                <textarea name="description" class="form-input" rows="3">{{ old('description') }}</textarea>
            </div>

            <div class="form-group">
                <label class="form-label" style="display:flex;align-items:center;gap:8px">
                    <input type="checkbox" name="is_published" value="1" {{ old('is_published', true) ? 'checked' : '' }}>
                    Опубликована
                </label>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                Сохранить коллекцию
            </button>
            <a href="{{ route('admin.vocabulary.collections.index') }}" class="btn btn-ghost">Отмена</a>
        </div>
    </form>
@endsection
