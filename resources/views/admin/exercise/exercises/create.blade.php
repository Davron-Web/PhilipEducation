@extends('layouts.admin')

@section('title', 'Создать упражнение')

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

    <x-admin.page-header title="Создать упражнение">
        <x-slot:sub><span class="live-dot"></span> Добавление нового упражнения в систему</x-slot:sub>
        <x-slot:actions>
            <a href="{{ route('admin.exercise.exercises.index') }}" class="btn btn-ghost">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
                К списку
            </a>
        </x-slot:actions>
    </x-admin.page-header>

    <form action="{{ route('admin.exercise.exercises.store') }}" method="POST" class="card">
        @csrf
        <div class="form-grid">
            <div class="form-group">
                <label class="form-label">Урок *</label>
                <select name="lesson_id" class="form-select" required>
                    <option value="">Выберите урок</option>
                    @foreach($lessons as $id => $title)
                        <option value="{{ $id }}" {{ old('lesson_id') == $id ? 'selected' : '' }}>{{ $title }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">Тип упражнения *</label>
                <select name="type" class="form-select" required>
                    <option value="">Выберите тип</option>
                    @foreach($types as $type => $label)
                        <option value="{{ $type }}" {{ old('type') == $type ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group full">
                <label class="form-label">Название *</label>
                <input type="text" name="title" class="form-input" placeholder="Например: Fill in the blanks" value="{{ old('title') }}" required>
            </div>

            <div class="form-group full">
                <label class="form-label">Инструкции *</label>
                <textarea name="instructions" class="form-textarea" placeholder="Опишите задание для ученика..." required>{{ old('instructions') }}</textarea>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                Создать упражнение
            </button>
            <a href="{{ route('admin.exercise.exercises.index') }}" class="btn btn-ghost">Отмена</a>
        </div>
    </form>
@endsection
