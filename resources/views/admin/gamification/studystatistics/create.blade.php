@extends('layouts.admin')

@section('title', 'Добавить статистику')

@section('content')
    <x-admin.page-header title="Добавить статистику" :backRoute="route('admin.gamification.studystatistics.index')" />

    <form action="{{ route('admin.gamification.studystatistics.store') }}" method="POST" class="card">
        @csrf
        <div class="form-grid">
            <div class="form-group full">
                <label class="form-label">Пользователь *</label>
                <select name="user_id" class="form-select @error('user_id') is-invalid @enderror" required>
                    <option value="">Выберите пользователя</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                    @endforeach
                </select>
                @error('user_id')<div class="error-text">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label class="form-label">Уроков пройдено</label>
                <input type="number" name="total_lessons_completed" class="form-input" value="{{ old('total_lessons_completed', 0) }}" min="0">
            </div>
            <div class="form-group">
                <label class="form-label">Тестов сдано</label>
                <input type="number" name="total_tests_passed" class="form-input" value="{{ old('total_tests_passed', 0) }}" min="0">
            </div>
            <div class="form-group">
                <label class="form-label">Слов изучено</label>
                <input type="number" name="total_words_learned" class="form-input" value="{{ old('total_words_learned', 0) }}" min="0">
            </div>
            <div class="form-group">
                <label class="form-label">Время изучения (мин)</label>
                <input type="number" name="study_time_minutes" class="form-input" value="{{ old('study_time_minutes', 0) }}" min="0">
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Создать</button>
            <a href="{{ route('admin.gamification.studystatistics.index') }}" class="btn btn-ghost">Отмена</a>
        </div>
    </form>
@endsection
