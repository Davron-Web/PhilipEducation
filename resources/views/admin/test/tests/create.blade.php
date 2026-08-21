@extends('layouts.admin')

@section('title', 'Создать тест')

@section('content')
    @php
        $lessons = $lessons ?? \App\Models\Content\Lesson::orderBy('title')->get();
    @endphp

    <x-admin.page-header title="Создать тест" :backRoute="route('admin.test.tests.index')" />

    <form action="{{ route('admin.test.tests.store') }}" method="POST" class="card">
        @csrf
        <div class="form-grid">
            <div class="form-group">
                <label class="form-label">Название теста *</label>
                <input type="text" name="title" class="form-input" placeholder="Например: Grammar Test A2" value="{{ old('title') }}" required>
            </div>

            <div class="form-group">
                <label class="form-label">Урок *</label>
                <select name="lesson_id" class="form-select" required>
                    <option value="">Выберите урок</option>
                    @foreach($lessons as $lesson)
                        <option value="{{ $lesson->id }}" {{ old('lesson_id') == $lesson->id ? 'selected' : '' }}>{{ $lesson->title }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group full">
                <label class="form-label">Проходной балл (0–100%)</label>
                <div class="meter">
                    <input type="range" name="passing_score" id="passing_score" min="0" max="100" step="1" value="{{ old('passing_score', 70) }}" style="width:100%;accent-color:var(--blue)">
                    <span id="scoreNum" style="font-size:22px;font-weight:800;color:var(--blue);min-width:56px;text-align:center">{{ old('passing_score', 70) }}%</span>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Лимит времени (минут)</label>
                <input type="number" name="time_limit" class="form-input" placeholder="Например: 30" min="1" max="600" value="{{ old('time_limit') }}">
                <span class="form-label" style="text-transform:none;font-weight:400">Оставьте пустым для неограниченного времени</span>
            </div>

            <div class="form-group">
                <label class="form-label">Статус публикации</label>
                <div class="form-check">
                    <label class="switch">
                        <input type="hidden" name="is_published" value="0">
                        <input type="checkbox" name="is_published" value="1" {{ old('is_published', 0) ? 'checked' : '' }}>
                        <span class="slider"></span>
                    </label>
                    <span>Опубликовать</span>
                </div>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                Создать тест
            </button>
            <a href="{{ route('admin.test.tests.index') }}" class="btn btn-ghost">Отмена</a>
        </div>
    </form>
@endsection

@push('scripts')
<script>
    const range = document.getElementById('passing_score'), num = document.getElementById('scoreNum');
    range.addEventListener('input', () => { num.textContent = range.value + '%'; });
</script>
@endpush
