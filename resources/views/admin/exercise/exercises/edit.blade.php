@extends('layouts.admin')

@section('title', 'Редактировать упражнение')

@section('content')
    @php
        $types = $types ?? [];

        $rawLessons = $lessons ?? null;
        if ($rawLessons === null && class_exists(\App\Models\Content\Lesson::class)) {
            try { $rawLessons = \App\Models\Content\Lesson::orderBy('title')->pluck('title', 'id')->toArray(); }
            catch (\Throwable $e) { $rawLessons = []; }
        }
        $lessons = [];
        if (is_iterable($rawLessons)) {
            foreach ($rawLessons as $key => $item) {
                if (is_object($item)) {
                    $id = $item->id ?? $key;
                    $lessons[$id] = $item->title ?? $item->name ?? ('Урок #' . $id);
                } elseif (is_array($item)) {
                    $id = $item['id'] ?? $key;
                    $lessons[$id] = $item['title'] ?? $item['name'] ?? ('Урок #' . $id);
                } else {
                    $lessons[$key] = (string) $item;
                }
            }
        }
    @endphp

    <x-admin.page-header :title="'Редактировать упражнение: ' . ($exercise->title ?? '')">
        <x-slot:sub><span class="live-dot"></span> ID: <b style="color:var(--text);margin-left:4px">#{{ $exercise->id ?? '' }}</b></x-slot:sub>
        <x-slot:actions>
            <a href="{{ route('admin.exercise.exercises.show', $exercise) }}" class="btn btn-ghost">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                Просмотр
            </a>
            <a href="{{ route('admin.exercise.exercises.index') }}" class="btn btn-ghost">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
                К списку
            </a>
        </x-slot:actions>
    </x-admin.page-header>

    <form action="{{ route('admin.exercise.exercises.update', $exercise) }}" method="POST" class="card">
        @csrf
        @method('PUT')
        <div class="form-grid">
            <div class="form-group">
                <label class="form-label">Урок *</label>
                <select name="lesson_id" class="form-select" required>
                    <option value="">Выберите урок</option>
                    @foreach($lessons as $id => $title)
                        <option value="{{ $id }}" {{ old('lesson_id', $exercise->lesson_id ?? null) == $id ? 'selected' : '' }}>{{ $title }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">Тип упражнения *</label>
                <select name="type" class="form-select" required>
                    <option value="">Выберите тип</option>
                    @foreach($types as $type => $label)
                        <option value="{{ $type }}" {{ old('type', $exercise->type ?? null) == $type ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group full">
                <label class="form-label">Название *</label>
                <input type="text" name="title" class="form-input" value="{{ old('title', $exercise->title ?? '') }}" required>
            </div>

            <div class="form-group full">
                <label class="form-label">Инструкции *</label>
                <textarea name="instructions" class="form-textarea" required>{{ old('instructions', $exercise->instructions ?? '') }}</textarea>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-warning">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                Сохранить изменения
            </button>
            <a href="{{ route('admin.exercise.exercises.index') }}" class="btn btn-ghost">Отмена</a>
        </div>
    </form>
@endsection
