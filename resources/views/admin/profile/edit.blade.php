@extends('layouts.admin')

@section('title', 'Редактировать профиль')

@section('content')
    <x-admin.page-header title="Редактировать профиль" :backRoute="route('admin.profile.show')" />

    <form action="{{ route('admin.profile.update') }}" method="POST" class="card">
        @csrf
        @method('PUT')
        <div class="form-grid">
            <div class="form-group">
                <label class="form-label">Имя *</label>
                <input type="text" name="name" class="form-input @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}" required>
                @error('name')<div class="error-text">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label class="form-label">Email *</label>
                <input type="email" name="email" class="form-input @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" required>
                @error('email')<div class="error-text">{{ $message }}</div>@enderror
            </div>
            <div class="form-group full">
                <label class="form-label">Новый пароль (оставьте пустым, чтобы не менять)</label>
                <input type="password" name="password" class="form-input @error('password') is-invalid @enderror" minlength="6">
                @error('password')<div class="error-text">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Сохранить изменения</button>
            <a href="{{ route('admin.profile.show') }}" class="btn btn-ghost">Отмена</a>
        </div>
    </form>
@endsection
