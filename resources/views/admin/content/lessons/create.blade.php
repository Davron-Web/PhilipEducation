@extends('layouts.admin')

@section('title', 'Добавить урок')

@section('content')
    @php
        $hasStatus = \Illuminate\Support\Facades\Schema::hasColumn('lessons', 'is_published');
        $levels = $levels ?? \App\Models\Level::orderBy('name')->get();
    @endphp

    <x-admin.page-header title="Добавить урок" :backRoute="route('admin.content.lessons.index')" />

    <form action="{{ route('admin.content.lessons.store') }}" method="POST" class="card">
        @csrf
        <h3>Данные урока</h3>
        <div class="form-grid">
            <div class="form-group full">
                <label class="form-label">Название урока *</label>
                <input type="text" name="title" class="form-input" value="{{ old('title') }}" placeholder="Например: Present Simple" required>
            </div>

            <div class="form-group">
                <label class="form-label">Уровень</label>
                <select name="level_id" class="form-select">
                    <option value="">— не выбран —</option>
                    @foreach($levels as $level)
                        <option value="{{ $level->id }}" {{ old('level_id') == $level->id ? 'selected' : '' }}>{{ $level->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">Порядковый номер</label>
                <input type="number" name="order_number" class="form-input" value="{{ old('order_number', 1) }}" min="1">
            </div>

            <div class="form-group">
                <label class="form-label">Длительность (минут)</label>
                <input type="number" name="estimated_minutes" class="form-input" value="{{ old('estimated_minutes', 30) }}" min="1">
            </div>

            @if($hasStatus)
                <div class="form-group">
                    <label class="form-label">Статус</label>
                    <div class="form-check">
                        <label class="switch">
                            <input type="hidden" name="is_published" value="0">
                            <input type="checkbox" name="is_published" value="1" {{ old('is_published', 1) ? 'checked' : '' }}>
                            <span class="slider"></span>
                        </label>
                        <span>Опубликовать урок</span>
                    </div>
                </div>
            @endif

            <div class="form-group full">
                <label class="form-label">Краткое описание</label>
                <textarea name="description" class="form-textarea" rows="3" placeholder="Короткое описание урока для списка и карточки">{{ old('description') }}</textarea>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                Сохранить урок
            </button>
            <a href="{{ route('admin.content.lessons.index') }}" class="btn btn-ghost">Отмена</a>
        </div>
    </form>

    <div class="alert-card" style="margin-top:16px">Полное содержание урока (текст, примеры, упражнения, тесты) добавляется на странице урока после его создания.</div>
@endsection
