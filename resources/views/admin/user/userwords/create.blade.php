@extends('layouts.admin')

@section('title', 'Добавить запись')

@section('content')
    <x-admin.page-header title="Добавить запись" :backRoute="route('admin.user.userwords.index')" />

    <form action="{{ route('admin.user.userwords.store') }}" method="POST" class="card">
        @csrf
        <div class="form-grid">
            <div class="form-group">
                <label class="form-label">Пользователь *</label>
                <select name="user_id" class="form-select" required>
                    <option value="">Выберите пользователя</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Слово *</label>
                <select name="word_id" class="form-select" required>
                    <option value="">Выберите слово</option>
                    @foreach($words as $word)
                        <option value="{{ $word->id }}" {{ old('word_id') == $word->id ? 'selected' : '' }}>{{ $word->word }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">Правильные ответы</label>
                <input type="number" name="correct_answers" class="form-input" value="{{ old('correct_answers', 0) }}" min="0">
            </div>
            <div class="form-group">
                <label class="form-label">Неправильные ответы</label>
                <input type="number" name="wrong_answers" class="form-input" value="{{ old('wrong_answers', 0) }}" min="0">
            </div>

            <div class="form-group">
                <label class="form-label">Последний повтор</label>
                <input type="datetime-local" name="last_reviewed_at" class="form-input" value="{{ old('last_reviewed_at') }}">
            </div>
            <div class="form-group">
                <label class="form-label">Статус</label>
                <div class="form-check">
                    <label class="switch">
                        <input type="checkbox" name="learned" value="1" {{ old('learned') ? 'checked' : '' }}>
                        <span class="slider"></span>
                    </label>
                    <span>Выучено</span>
                </div>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Создать</button>
            <a href="{{ route('admin.user.userwords.index') }}" class="btn btn-ghost">Отмена</a>
        </div>
    </form>
@endsection
