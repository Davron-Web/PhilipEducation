@extends('layouts.admin')

@section('title', 'Редактировать правило')

@section('content')
    @php
        $topic  = $grammartopic ?? $grammarTopic ?? $grammar ?? $topic ?? null;
        if (!$topic) abort(404);
        $levels = $levels ?? \App\Models\System\Level::orderBy('name')->get();
    @endphp

    <x-admin.page-header title="Редактировать правило" :backRoute="route('admin.content.grammartopics.index')">
        <x-slot:actions>
            <form action="{{ route('admin.content.grammartopics.destroy', $topic->id) }}" method="POST" onsubmit="return confirm('Удалить правило?')" style="margin:0">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">Удалить правило</button>
            </form>
        </x-slot:actions>
    </x-admin.page-header>

    <form action="{{ route('admin.content.grammartopics.update', $topic->id) }}" method="POST" class="card">
        @csrf
        @method('PUT')
        <h3>Данные правила</h3>
        <div class="form-grid">
            <div class="form-group full">
                <label class="form-label">Название правила *</label>
                <input type="text" name="title" class="form-input" value="{{ old('title', $topic->title) }}" required>
            </div>

            <div class="form-group">
                <label class="form-label">Уровень</label>
                <select name="level_id" class="form-select">
                    <option value="">— не выбран —</option>
                    @foreach($levels as $level)
                        <option value="{{ $level->id }}" {{ old('level_id', $topic->level_id) == $level->id ? 'selected' : '' }}>{{ $level->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">Порядковый номер</label>
                <input type="number" name="order_number" class="form-input" value="{{ old('order_number', $topic->order_number) }}" min="1">
            </div>

            <div class="form-group full">
                <label class="form-label">Теория правила *</label>
                <textarea name="theory_content" class="form-textarea" required>{{ old('theory_content', $topic->theory_content ?? $topic->theory) }}</textarea>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                Сохранить изменения
            </button>
            <a href="{{ route('admin.content.grammartopics.show', $topic->id) }}" class="btn btn-ghost">Отмена</a>
        </div>
    </form>
@endsection
