@extends('layouts.admin')

@section('title', 'Редактировать ответ теста')

@section('content')
    <x-admin.page-header :title="'Редактировать ответ #' . $testAnswer->id" :backRoute="route('admin.test.testanswers.index')" />

    <form action="{{ route('admin.test.testanswers.update', $testAnswer) }}" method="POST" class="card">
        @csrf
        @method('PUT')
        <div class="form-grid">
            <div class="form-group full">
                <label class="form-label">Вопрос *</label>
                <select name="question_id" class="form-select" required>
                    <option value="">Выберите вопрос</option>
                    @foreach($questions as $question)
                        <option value="{{ $question->id }}" {{ old('question_id', $testAnswer->question_id) == $question->id ? 'selected' : '' }}>{{ $question->question }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group full">
                <label class="form-label">Ответ *</label>
                <input type="text" name="answer" class="form-input" value="{{ old('answer', $testAnswer->answer) }}" required>
            </div>
            <div class="form-group full">
                <div class="form-check">
                    <label class="switch">
                        <input type="checkbox" name="is_correct" value="1" {{ old('is_correct', $testAnswer->is_correct) ? 'checked' : '' }}>
                        <span class="slider"></span>
                    </label>
                    <span>Это правильный ответ</span>
                </div>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Сохранить изменения</button>
            <a href="{{ route('admin.test.testanswers.index') }}" class="btn btn-ghost">Отмена</a>
        </div>
    </form>
@endsection
