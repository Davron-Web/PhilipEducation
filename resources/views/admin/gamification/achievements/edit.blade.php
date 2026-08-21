@extends('layouts.admin')

@section('title', 'Редактировать достижение')

@section('content')
    <x-admin.page-header :title="'Редактировать: ' . $achievement->title" :backRoute="route('admin.gamification.achievements.index')" />

    <form action="{{ route('admin.gamification.achievements.update', $achievement) }}" method="POST" class="card">
        @csrf
        @method('PUT')
        <div class="form-grid">
            <div class="form-group full">
                <label class="form-label">Название *</label>
                <input type="text" name="title" class="form-input @error('title') is-invalid @enderror" value="{{ old('title', $achievement->title) }}" required>
                @error('title')<div class="error-text">{{ $message }}</div>@enderror
            </div>
            <div class="form-group full">
                <label class="form-label">Описание</label>
                <textarea name="description" class="form-textarea" rows="3">{{ old('description', $achievement->description) }}</textarea>
            </div>
            <div class="form-group full">
                <label class="form-label">Иконка</label>
                <input type="text" name="icon" class="form-input @error('icon') is-invalid @enderror" value="{{ old('icon', $achievement->icon) }}" placeholder="URL изображения или CSS-класс иконки">
                @error('icon')<div class="error-text">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label class="form-label">Баллы *</label>
                <input type="number" name="points" class="form-input @error('points') is-invalid @enderror" value="{{ old('points', $achievement->points) }}" min="0" required>
                @error('points')<div class="error-text">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Сохранить изменения</button>
            <a href="{{ route('admin.gamification.achievements.index') }}" class="btn btn-ghost">Отмена</a>
        </div>
    </form>
@endsection
