@extends('layouts.admin')

@section('title', 'Редактировать запись')

@section('content')
    <x-admin.page-header :title="'Редактировать запись #' . $userWord->id" :backRoute="route('admin.user.userwords.index')" />

    <form action="{{ route('admin.user.userwords.update', $userWord) }}" method="POST" class="card">
        @csrf
        @method('PUT')
        <div class="form-grid">
            <div class="form-group">
                <label class="form-label">Пользователь *</label>
                <select name="user_id" class="form-select" required>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ old('user_id', $userWord->user_id) == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Слово *</label>
                <select name="word_id" class="form-select" required>
                    @foreach($words as $word)
                        <option value="{{ $word->id }}" {{ old('word_id', $userWord->word_id) == $word->id ? 'selected' : '' }}>{{ $word->word }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">Правильные ответы</label>
                <input type="number" name="correct_answers" class="form-input" value="{{ old('correct_answers', $userWord->correct_answers) }}" min="0">
            </div>
            <div class="form-group">
                <label class="form-label">Неправильные ответы</label>
                <input type="number" name="wrong_answers" class="form-input" value="{{ old('wrong_answers', $userWord->wrong_answers) }}" min="0">
            </div>

            <div class="form-group">
                <label class="form-label">Последний повтор</label>
                <input type="datetime-local" name="last_reviewed_at" class="form-input" value="{{ old('last_reviewed_at', optional($userWord->last_reviewed_at)->format('Y-m-d\TH:i')) }}">
            </div>
            <div class="form-group">
                <label class="form-label">Статус</label>
                <div class="form-check">
                    <label class="switch">
                        <input type="checkbox" name="learned" value="1" {{ old('learned', $userWord->learned) ? 'checked' : '' }}>
                        <span class="slider"></span>
                    </label>
                    <span>Выучено</span>
                </div>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Сохранить изменения</button>
            <a href="{{ route('admin.user.userwords.index') }}" class="btn btn-ghost">Отмена</a>
        </div>
    </form>
@endsection
