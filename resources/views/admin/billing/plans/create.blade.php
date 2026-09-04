@extends('layouts.admin')

@section('title', 'Добавить тариф')

@section('content')
    <x-admin.page-header title="Добавить тариф" :backRoute="route('admin.billing.plans.index')" />

    <form action="{{ route('admin.billing.plans.store') }}" method="POST" class="card">
        @csrf
        <div class="form-grid">
            <div class="form-group">
                <label class="form-label">Код *</label>
                <input type="text" name="code" class="form-input @error('code') is-invalid @enderror" value="{{ old('code', $plan->code ?? '') }}" placeholder="monthly" required>
                @error('code')<div class="error-text">{{ $message }}</div>@enderror
                <div class="form-hint">Латиницей — по нему тариф находится в коде.</div>
            </div>
            <div class="form-group">
                <label class="form-label">Название *</label>
                <input type="text" name="name" class="form-input @error('name') is-invalid @enderror" value="{{ old('name', $plan->name ?? '') }}" required>
                @error('name')<div class="error-text">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label class="form-label">Срок, дней *</label>
                <input type="number" name="duration_days" class="form-input @error('duration_days') is-invalid @enderror" value="{{ old('duration_days', $plan->duration_days ?? 30) }}" min="1" required>
                @error('duration_days')<div class="error-text">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label class="form-label">Цена *</label>
                <input type="number" step="0.01" name="price" class="form-input @error('price') is-invalid @enderror" value="{{ old('price', isset($plan) ? $plan->price_minor / 100 : '') }}" min="0" required>
                @error('price')<div class="error-text">{{ $message }}</div>@enderror
                <div class="form-hint">В основной валюте, например 79.00 — хранится в дирамах.</div>
            </div>
            <div class="form-group">
                <label class="form-label">Валюта *</label>
                <input type="text" name="currency" class="form-input @error('currency') is-invalid @enderror" value="{{ old('currency', $plan->currency ?? 'TJS') }}" maxlength="3" required>
                @error('currency')<div class="error-text">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label class="form-label">Порядок *</label>
                <input type="number" name="sort_order" class="form-input" value="{{ old('sort_order', $plan->sort_order ?? 0) }}" min="0" required>
                <div class="form-hint">Чем меньше, тем выше на странице цен.</div>
            </div>
            <div class="form-group full">
                <label class="form-label">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $plan->is_active ?? true) ? 'checked' : '' }}>
                    Активен — показывается на странице цен
                </label>
            </div>
        </div>
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Создать</button>
            <a href="{{ route('admin.billing.plans.index') }}" class="btn btn-ghost">Отмена</a>
        </div>
    </form>
@endsection