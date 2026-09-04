@extends('layouts.admin')

@section('title', 'Добавить титул')

@section('content')
    <x-admin.page-header title="Добавить титул" :backRoute="route('admin.gamification.titles.index')" />

    <form action="{{ route('admin.gamification.titles.store') }}" method="POST" class="card">
        @csrf
        <div class="form-grid">
            <div class="form-group">
                <label class="form-label">Код *</label>
                <input type="text" name="code" class="form-input @error('code') is-invalid @enderror" value="{{ old('code', $title->code ?? '') }}" placeholder="expert" required>
                @error('code')<div class="error-text">{{ $message }}</div>@enderror
                <div class="form-hint">Латиницей, без пробелов — используется в коде и не показывается ученикам.</div>
            </div>
            <div class="form-group">
                <label class="form-label">Название *</label>
                <input type="text" name="name" class="form-input @error('name') is-invalid @enderror" value="{{ old('name', $title->name ?? '') }}" required>
                @error('name')<div class="error-text">{{ $message }}</div>@enderror
            </div>
            <div class="form-group full">
                <label class="form-label">Описание</label>
                <textarea name="description" class="form-textarea" rows="2">{{ old('description', $title->description ?? '') }}</textarea>
            </div>
            <div class="form-group">
                <label class="form-label">Значок</label>
                <input type="text" name="icon" class="form-input" value="{{ old('icon', $title->icon ?? '') }}" placeholder="🏆">
                <div class="form-hint">Один эмодзи.</div>
            </div>
            <div class="form-group">
                <label class="form-label">Порог XP *</label>
                <input type="number" name="min_xp" class="form-input @error('min_xp') is-invalid @enderror" value="{{ old('min_xp', $title->min_xp ?? 0) }}" min="0" required>
                @error('min_xp')<div class="error-text">{{ $message }}</div>@enderror
                <div class="form-hint">Титул выдаётся, когда опыт ученика достигает этого значения.</div>
            </div>
            <div class="form-group full">
                <label class="form-label">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $title->is_active ?? true) ? 'checked' : '' }}>
                    Активен — выдаётся новым ученикам
                </label>
            </div>
        </div>
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Создать</button>
            <a href="{{ route('admin.gamification.titles.index') }}" class="btn btn-ghost">Отмена</a>
        </div>
    </form>
@endsection