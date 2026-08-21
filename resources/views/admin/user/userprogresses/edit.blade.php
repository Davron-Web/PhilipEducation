@extends('layouts.admin')

@section('title', 'Редактировать прогресс')

@section('content')
    <x-admin.page-header :title="'Редактировать прогресс #' . $userProgress->id" :backRoute="route('admin.user.userprogresses.index')" />

    <form action="{{ route('admin.user.userprogresses.update', $userProgress) }}" method="POST" class="card">
        @csrf
        @method('PUT')
        <div class="form-grid">
            <div class="form-group">
                <label class="form-label">Пользователь *</label>
                <select name="user_id" class="form-select @error('user_id') is-invalid @enderror" required>
                    <option value="">Выберите пользователя</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ old('user_id', $userProgress->user_id) == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                    @endforeach
                </select>
                @error('user_id')<div class="error-text">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label class="form-label">Урок *</label>
                <select name="lesson_id" class="form-select @error('lesson_id') is-invalid @enderror" required>
                    <option value="">Выберите урок</option>
                    @foreach($lessons as $lesson)
                        <option value="{{ $lesson->id }}" {{ old('lesson_id', $userProgress->lesson_id) == $lesson->id ? 'selected' : '' }}>{{ $lesson->title }}</option>
                    @endforeach
                </select>
                @error('lesson_id')<div class="error-text">{{ $message }}</div>@enderror
            </div>
            <div class="form-group full">
                <label class="form-label">Прогресс (%) *</label>
                <input type="range" name="progress_percent" min="0" max="100" value="{{ old('progress_percent', $userProgress->progress_percent) }}" oninput="this.nextElementSibling.value = this.value">
                <output>{{ $userProgress->progress_percent }}</output>
                @error('progress_percent')<div class="error-text">{{ $message }}</div>@enderror
            </div>
            <div class="form-group full">
                <div class="form-check">
                    <label class="switch">
                        <input type="checkbox" name="is_completed" value="1" {{ old('is_completed', $userProgress->is_completed) ? 'checked' : '' }}>
                        <span class="slider"></span>
                    </label>
                    <span>Завершено</span>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Время (секунды)</label>
                <input type="number" name="time_spent" class="form-input" value="{{ old('time_spent', $userProgress->time_spent) }}" min="0">
            </div>
            <div class="form-group">
                <label class="form-label">Последняя позиция</label>
                <input type="number" name="last_position" class="form-input" value="{{ old('last_position', $userProgress->last_position) }}" min="0">
            </div>
            <div class="form-group full">
                <label class="form-label">Дата завершения</label>
                <input type="datetime-local" name="completed_at" class="form-input" value="{{ old('completed_at', $userProgress->completed_at?->format('Y-m-d\TH:i')) }}">
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Сохранить изменения</button>
            <a href="{{ route('admin.user.userprogresses.index') }}" class="btn btn-ghost">Отмена</a>
        </div>
    </form>
@endsection
