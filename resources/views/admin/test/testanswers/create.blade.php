@extends('layouts.admin')

@section('title', 'Добавить ответ теста')

@section('content')
    <x-admin.page-header title="Добавить ответ теста" :backRoute="route('admin.test.testanswers.index')" />

    <form action="{{ route('admin.test.testanswers.store') }}" method="POST" class="card">
        @csrf
        <div class="form-grid">
            <div class="form-group full">
                <label class="form-label">Вопрос *</label>
                <select name="question_id" class="form-select" required>
                    <option value="">Выберите вопрос</option>
                    @foreach($questions as $question)
                        <option value="{{ $question->id }}" {{ old('question_id') == $question->id ? 'selected' : '' }}>{{ $question->question }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group full">
                <label class="form-label">Ответ *</label>
                <input type="text" name="answer" class="form-input" value="{{ old('answer') }}" required>
            </div>
            <div class="form-group full">
                <div class="form-check">
                    <label class="switch">
                        <input type="checkbox" name="is_correct" value="1" {{ old('is_correct') ? 'checked' : '' }}>
                        <span class="slider"></span>
                    </label>
                    <span>Это правильный ответ</span>
                </div>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Создать</button>
            <a href="{{ route('admin.test.testanswers.index') }}" class="btn btn-ghost">Отмена</a>
        </div>
    </form>
@endsection
