@extends('layouts.admin')

@section('title', 'Выдать достижение')

@section('content')
    <x-admin.page-header title="Выдать достижение" :backRoute="route('admin.user.userachievements.index')" />

    <form action="{{ route('admin.user.userachievements.store') }}" method="POST" class="card">
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
                <label class="form-label">Достижение *</label>
                <select name="achievement_id" class="form-select @error('achievement_id') is-invalid @enderror" required>
                    <option value="">Выберите достижение</option>
                    @foreach($achievements as $achievement)
                        <option value="{{ $achievement->id }}" {{ old('achievement_id') == $achievement->id ? 'selected' : '' }}>{{ $achievement->title }}</option>
                    @endforeach
                </select>
                @error('achievement_id')<div class="error-text">{{ $message }}</div>@enderror
            </div>
            <div class="form-group full">
                <label class="form-label">Дата получения *</label>
                <input type="datetime-local" name="earned_at" class="form-input @error('earned_at') is-invalid @enderror" value="{{ old('earned_at', now()->format('Y-m-d\TH:i')) }}" required>
                @error('earned_at')<div class="error-text">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Выдать достижение</button>
            <a href="{{ route('admin.user.userachievements.index') }}" class="btn btn-ghost">Отмена</a>
        </div>
    </form>
@endsection
