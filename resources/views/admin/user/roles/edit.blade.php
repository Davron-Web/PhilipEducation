@extends('layouts.admin')

@section('title', 'Редактировать роль')

@section('content')
    <x-admin.page-header :title="'Редактировать роль: ' . $role->name" :backRoute="route('admin.user.roles.index')" />

    <form action="{{ route('admin.user.roles.update', $role) }}" method="POST" class="card">
        @csrf
        @method('PUT')
        <div class="form-grid">
            <div class="form-group full">
                <label class="form-label">Название *</label>
                <input type="text" name="name" class="form-input @error('name') is-invalid @enderror" value="{{ old('name', $role->name) }}" maxlength="50" required>
                @error('name')<div class="error-text">{{ $message }}</div>@enderror
            </div>
            <div class="form-group full">
                <label class="form-label">Описание</label>
                <input type="text" name="description" class="form-input @error('description') is-invalid @enderror" value="{{ old('description', $role->description) }}" maxlength="255">
                @error('description')<div class="error-text">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Сохранить изменения</button>
            <a href="{{ route('admin.user.roles.index') }}" class="btn btn-ghost">Отмена</a>
        </div>
    </form>
@endsection
