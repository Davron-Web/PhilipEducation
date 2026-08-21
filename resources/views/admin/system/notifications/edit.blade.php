@extends('layouts.admin')

@section('title', 'Редактировать уведомление')

@section('content')
    <x-admin.page-header :title="'Редактировать уведомление #' . $notification->id" :backRoute="route('admin.system.notifications.index')" />

    <form action="{{ route('admin.system.notifications.update', $notification) }}" method="POST" class="card">
        @csrf
        @method('PUT')
        <div class="form-grid">
            <div class="form-group full">
                <label class="form-label">Получатель *</label>
                <select name="user_id" class="form-select @error('user_id') is-invalid @enderror" required>
                    <option value="">Выберите пользователя</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ old('user_id', $notification->user_id) == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                    @endforeach
                </select>
                @error('user_id')<div class="error-text">{{ $message }}</div>@enderror
            </div>
            <div class="form-group full">
                <label class="form-label">Название *</label>
                <input type="text" name="title" class="form-input @error('title') is-invalid @enderror" value="{{ old('title', $notification->title) }}" required>
                @error('title')<div class="error-text">{{ $message }}</div>@enderror
            </div>
            <div class="form-group full">
                <label class="form-label">Сообщение *</label>
                <textarea name="message" class="form-textarea @error('message') is-invalid @enderror" rows="5" required>{{ old('message', $notification->message) }}</textarea>
                @error('message')<div class="error-text">{{ $message }}</div>@enderror
            </div>
            <div class="form-group full">
                <div class="form-check">
                    <label class="switch">
                        <input type="checkbox" name="is_read" value="1" {{ old('is_read', $notification->is_read) ? 'checked' : '' }}>
                        <span class="slider"></span>
                    </label>
                    <span>Прочитано</span>
                </div>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Сохранить изменения</button>
            <a href="{{ route('admin.system.notifications.index') }}" class="btn btn-ghost">Отмена</a>
        </div>
    </form>
@endsection
