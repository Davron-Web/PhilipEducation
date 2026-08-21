@extends('layouts.admin')

@section('title', 'Добавить вопрос теста')

@section('content')
    <x-admin.page-header title="Добавить вопрос теста" :backRoute="route('admin.test.testquestions.index')" />

    <form action="{{ route('admin.test.testquestions.store') }}" method="POST" class="card">
        @csrf
        <div class="form-grid">
            <div class="form-group">
                <label class="form-label">Тест *</label>
                <select name="test_id" class="form-select" required>
                    <option value="">Выберите тест</option>
                    @foreach($tests as $test)
                        <option value="{{ $test->id }}" {{ old('test_id') == $test->id ? 'selected' : '' }}>{{ $test->title }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Тип</label>
                <select name="type" class="form-select">
                    <option value="">Выберите тип</option>
                    @foreach($types as $type => $label)
                        <option value="{{ $type }}" {{ old('type') == $type ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group full">
                <label class="form-label">Вопрос *</label>
                <textarea name="question" class="form-textarea" required>{{ old('question') }}</textarea>
            </div>

            <div class="form-group">
                <label class="form-label">Баллы *</label>
                <input type="number" name="points" class="form-input" value="{{ old('points', 1) }}" min="1" required>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Создать</button>
            <a href="{{ route('admin.test.testquestions.index') }}" class="btn btn-ghost">Отмена</a>
        </div>
    </form>
@endsection
