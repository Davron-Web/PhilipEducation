@extends('layouts.admin')

@section('title', 'Добавить вопрос')

@section('content')
    <x-admin.page-header title="Добавить вопрос" :backRoute="route('admin.exercise.exercisequestions.index')" />

    <form action="{{ route('admin.exercise.exercisequestions.store') }}" method="POST" class="card">
        @csrf
        <div class="form-grid">
            <div class="form-group full">
                <label class="form-label">Упражнение *</label>
                <select name="exercise_id" class="form-select" required>
                    <option value="">Выберите упражнение</option>
                    @foreach($exercises as $exercise)
                        <option value="{{ $exercise->id }}" {{ old('exercise_id') == $exercise->id ? 'selected' : '' }}>{{ $exercise->title }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group full">
                <label class="form-label">Вопрос *</label>
                <textarea name="question" class="form-textarea" required>{{ old('question') }}</textarea>
            </div>
            <div class="form-group full">
                <label class="form-label">Правильный ответ *</label>
                <textarea name="correct_answer" class="form-textarea" required>{{ old('correct_answer') }}</textarea>
            </div>
        </div>
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Создать</button>
            <a href="{{ route('admin.exercise.exercisequestions.index') }}" class="btn btn-ghost">Отмена</a>
        </div>
    </form>
@endsection
