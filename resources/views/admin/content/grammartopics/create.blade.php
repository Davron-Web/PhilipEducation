@extends('layouts.admin')

@section('title', 'Добавить правило')

@section('content')
    @php
        $levels = $levels ?? \App\Models\System\Level::orderBy('name')->get();
    @endphp

    <x-admin.page-header title="Добавить правило" :backRoute="route('admin.content.grammartopics.index')" />

    <form action="{{ route('admin.content.grammartopics.store') }}" method="POST" class="card">
        @csrf
        <h3>Данные правила</h3>
        <div class="form-grid">
            <div class="form-group full">
                <label class="form-label">Название правила *</label>
                <input type="text" name="title" class="form-input" value="{{ old('title') }}" placeholder="Например: Present Simple" required>
            </div>

            <div class="form-group">
                <label class="form-label">Уровень</label>
                <select name="level_id" class="form-select">
                    <option value="">— не выбран —</option>
                    @foreach($levels as $level)
                        <option value="{{ $level->id }}" {{ old('level_id') == $level->id ? 'selected' : '' }}>{{ $level->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">Порядковый номер</label>
                <input type="number" name="order_number" class="form-input" value="{{ old('order_number', 1) }}" min="1">
            </div>

            <div class="form-group full">
                <label class="form-label">Теория правила *</label>
                <textarea name="theory_content" class="form-textarea"
                          placeholder="Правило, исключения, примеры..."
                          required>{{ old('theory_content') }}</textarea>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                Сохранить правило
            </button>
            <a href="{{ route('admin.content.grammartopics.index') }}" class="btn btn-ghost">Отмена</a>
        </div>
    </form>
@endsection
