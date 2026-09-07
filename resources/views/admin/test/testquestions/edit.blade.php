@extends('layouts.admin')

@section('title', 'Редактировать вопрос теста')

@section('content')
    <x-admin.page-header :title="'Редактировать вопрос #' . $testQuestion->id" :backRoute="route('admin.test.testquestions.index')" />

    <form action="{{ route('admin.test.testquestions.update', $testQuestion) }}" method="POST" class="card">
        @csrf
        @method('PUT')
        <div class="form-grid">
            <div class="form-group">
                <label class="form-label">Тест *</label>
                <select name="test_id" class="form-select" required>
                    <option value="">Выберите тест</option>
                    @foreach($tests as $test)
                        <option value="{{ $test->id }}" {{ old('test_id', $testQuestion->test_id) == $test->id ? 'selected' : '' }}>{{ $test->title }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Тип</label>
                <select name="type" class="form-select">
                    <option value="">Выберите тип</option>
                    @foreach($types as $type => $label)
                        <option value="{{ $type }}" {{ old('type', $testQuestion->type) == $type ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group full">
                <label class="form-label">Вопрос *</label>
                <textarea name="question" class="form-textarea" required>{{ old('question', $testQuestion->question) }}</textarea>
            </div>

            <div class="form-group">
                <label class="form-label">Тема</label>
                <input type="text" name="topic" class="form-input" value="{{ old('topic', $testQuestion->topic ?? '') }}" placeholder="present-simple" maxlength="80">
                <div class="form-hint">Латиницей через дефис. По теме считается статистика ошибок ученика.</div>
            </div>

            <div class="form-group full">
                <label class="form-label">Объяснение</label>
                <textarea name="explanation" class="form-textarea" rows="3" placeholder="Почему правильный ответ именно такой">{{ old('explanation', $testQuestion->explanation ?? '') }}</textarea>
                <div class="form-hint">Показывается ученику только после неверного ответа.</div>
            </div>
            <div class="form-group">
                <label class="form-label">Баллы *</label>
                <input type="number" name="points" class="form-input" value="{{ old('points', $testQuestion->points) }}" min="1" required>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Сохранить изменения</button>
            <a href="{{ route('admin.test.testquestions.index') }}" class="btn btn-ghost">Отмена</a>
        </div>
    </form>
@endsection
