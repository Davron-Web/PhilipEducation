@extends('layouts.admin')

@section('title', 'Добавить прогресс')

@section('content')
    <x-admin.page-header title="Добавить прогресс" :backRoute="route('admin.user.userprogresses.index')" />

    <form action="{{ route('admin.user.userprogresses.store') }}" method="POST" class="card">
        @csrf
        <div class="form-grid">
            <div class="form-group">
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
                <label class="form-label">Урок *</label>
                <select name="lesson_id" class="form-select @error('lesson_id') is-invalid @enderror" required>
                    <option value="">Выберите урок</option>
                    @foreach($lessons as $lesson)
                        <option value="{{ $lesson->id }}" {{ old('lesson_id') == $lesson->id ? 'selected' : '' }}>{{ $lesson->title }}</option>
                    @endforeach
                </select>
                @error('lesson_id')<div class="error-text">{{ $message }}</div>@enderror
            </div>
            <div class="form-group full">
                <label class="form-label">Прогресс (%) *</label>
                <input type="range" name="progress_percent" min="0" max="100" value="{{ old('progress_percent', 0) }}" oninput="this.nextElementSibling.value = this.value">
                <output>0</output>
                @error('progress_percent')<div class="error-text">{{ $message }}</div>@enderror
            </div>
            <div class="form-group full">
                <div class="form-check">
                    <label class="switch">
                        <input type="checkbox" name="is_completed" value="1" {{ old('is_completed') ? 'checked' : '' }}>
                        <span class="slider"></span>
                    </label>
                    <span>Завершено</span>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Время (секунды)</label>
                <input type="number" name="time_spent" class="form-input" value="{{ old('time_spent', 0) }}" min="0">
                <small style="color:var(--muted)">Общее время на уроке в секундах</small>
            </div>
            <div class="form-group">
                <label class="form-label">Последняя позиция</label>
                <input type="number" name="last_position" class="form-input" value="{{ old('last_position', 0) }}" min="0">
                <small style="color:var(--muted)">Последняя просмотренная позиция контента</small>
            </div>
            <div class="form-group full">
                <label class="form-label">Дата завершения</label>
                <input type="datetime-local" name="completed_at" class="form-input" value="{{ old('completed_at') }}">
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Создать</button>
            <a href="{{ route('admin.user.userprogresses.index') }}" class="btn btn-ghost">Отмена</a>
        </div>
    </form>
@endsection
