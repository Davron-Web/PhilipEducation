@extends('layouts.admin')

@section('title', 'Добавить результат')

@section('content')
    <x-admin.page-header title="Добавить результат" :backRoute="route('admin.user.userresults.index')" />

    <form action="{{ route('admin.user.userresults.store') }}" method="POST" class="card">
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
                <label class="form-label">Тест *</label>
                <select name="test_id" class="form-select @error('test_id') is-invalid @enderror" required>
                    <option value="">Выберите тест</option>
                    @foreach($tests as $test)
                        <option value="{{ $test->id }}" {{ old('test_id') == $test->id ? 'selected' : '' }}>{{ $test->title }}</option>
                    @endforeach
                </select>
                @error('test_id')<div class="error-text">{{ $message }}</div>@enderror
            </div>
            <div class="form-group full">
                <label class="form-label">Балл (%) *</label>
                <input type="range" name="score" min="0" max="100" value="{{ old('score', 0) }}" oninput="this.nextElementSibling.value = this.value">
                <output>0</output>
                @error('score')<div class="error-text">{{ $message }}</div>@enderror
            </div>
            <div class="form-group full">
                <div class="form-check">
                    <label class="switch">
                        <input type="checkbox" name="passed" value="1" {{ old('passed') ? 'checked' : '' }}>
                        <span class="slider"></span>
                    </label>
                    <span>Сдано</span>
                </div>
            </div>
            <div class="form-group full">
                <label class="form-label">Дата попытки *</label>
                <input type="datetime-local" name="attempt_date" class="form-input @error('attempt_date') is-invalid @enderror" value="{{ old('attempt_date', now()->format('Y-m-d\TH:i')) }}" required>
                @error('attempt_date')<div class="error-text">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Создать</button>
            <a href="{{ route('admin.user.userresults.index') }}" class="btn btn-ghost">Отмена</a>
        </div>
    </form>
@endsection
