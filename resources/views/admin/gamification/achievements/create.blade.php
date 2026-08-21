@extends('layouts.admin')

@section('title', 'Добавить достижение')

@section('content')
    <x-admin.page-header title="Добавить достижение" :backRoute="route('admin.gamification.achievements.index')" />

    <form action="{{ route('admin.gamification.achievements.store') }}" method="POST" class="card">
        @csrf
        <div class="form-grid">
            <div class="form-group full">
                <label class="form-label">Название *</label>
                <input type="text" name="title" class="form-input @error('title') is-invalid @enderror" value="{{ old('title') }}" required>
                @error('title')<div class="error-text">{{ $message }}</div>@enderror
            </div>
            <div class="form-group full">
                <label class="form-label">Описание</label>
                <textarea name="description" class="form-textarea" rows="3">{{ old('description') }}</textarea>
            </div>
            <div class="form-group full">
                <label class="form-label">Иконка</label>
                <input type="text" name="icon" class="form-input @error('icon') is-invalid @enderror" value="{{ old('icon') }}" placeholder="URL изображения или CSS-класс иконки">
                @error('icon')<div class="error-text">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label class="form-label">Баллы *</label>
                <input type="number" name="points" class="form-input @error('points') is-invalid @enderror" value="{{ old('points', 0) }}" min="0" required>
                @error('points')<div class="error-text">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Создать</button>
            <a href="{{ route('admin.gamification.achievements.index') }}" class="btn btn-ghost">Отмена</a>
        </div>
    </form>
@endsection
