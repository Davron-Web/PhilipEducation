@extends('layouts.admin')

@section('title', 'Добавить пользователя')

@section('content')
    <x-admin.page-header title="Добавить пользователя" :backRoute="route('admin.user.users.index')" />

    <form action="{{ route('admin.user.users.store') }}" method="POST" class="card">
        @csrf
        <div class="form-grid">
            <div class="form-group">
                <label class="form-label">Имя *</label>
                <input type="text" name="name" class="form-input @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                @error('name')<div class="error-text">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label class="form-label">Email *</label>
                <input type="email" name="email" class="form-input @error('email') is-invalid @enderror" value="{{ old('email') }}" required>
                @error('email')<div class="error-text">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label class="form-label">Пароль *</label>
                <input type="password" name="password" class="form-input @error('password') is-invalid @enderror" required>
                @error('password')<div class="error-text">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label class="form-label">Роль</label>
                <select name="role_id" class="form-select">
                    @foreach(\App\Models\User\Role::all() as $role)
                        <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>{{ $role->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Уровень</label>
                <select name="level_id" class="form-select">
                    <option value="">Не указан</option>
                    @foreach(\App\Models\System\Level::all() as $level)
                        <option value="{{ $level->id }}" {{ old('level_id') == $level->id ? 'selected' : '' }}>{{ $level->code }} — {{ $level->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Баллы</label>
                <input type="number" name="points" class="form-input" value="{{ old('points', 0) }}">
            </div>
            <div class="form-group full">
                <div class="form-check">
                    <label class="switch">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                        <span class="slider"></span>
                    </label>
                    <span>Активен</span>
                </div>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Создать</button>
            <a href="{{ route('admin.user.users.index') }}" class="btn btn-ghost">Отмена</a>
        </div>
    </form>
@endsection
