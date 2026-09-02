@extends('layouts.admin')

@section('title', 'Редактировать выражение')

@section('content')
    @php
        $levels = $levels ?? \App\Models\System\Level::orderBy('id')->get();
        $types = [
            'idiom' => 'Идиома',
            'phrasal_verb' => 'Фразовый глагол',
            'proverb' => 'Пословица',
            'collocation' => 'Коллокация',
        ];
    @endphp

    <x-admin.page-header :title="'Редактировать выражение: ' . $expression->text" :backRoute="route('admin.vocabulary.expressions.index')">
        <x-slot:actions>
            <a href="{{ route('admin.vocabulary.expressions.show', $expression) }}" class="btn btn-ghost">Просмотр</a>
        </x-slot:actions>
    </x-admin.page-header>

    <form action="{{ route('admin.vocabulary.expressions.update', $expression) }}" method="POST" class="card">
        @csrf
        @method('PUT')
        <div class="form-grid">
            <div class="form-group">
                <label class="form-label">Выражение *</label>
                <input type="text" name="text" class="form-input" value="{{ old('text', $expression->text) }}" required>
            </div>
            <div class="form-group">
                <label class="form-label">Тип *</label>
                <select name="type" class="form-select" required>
                    @foreach($types as $key => $label)
                        <option value="{{ $key }}" {{ old('type', $expression->type) === $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">Транскрипция</label>
                <input type="text" name="transcription" class="form-input" value="{{ old('transcription', $expression->transcription) }}">
            </div>
            <div class="form-group">
                <label class="form-label">Уровень</label>
                <select name="level_id" class="form-select">
                    <option value="">Без уровня</option>
                    @foreach($levels as $level)
                        <option value="{{ $level->id }}" {{ old('level_id', $expression->level_id) == $level->id ? 'selected' : '' }}>{{ $level->code }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">Категория</label>
                <input type="text" name="category" class="form-input" value="{{ old('category', $expression->category) }}">
            </div>
            <div class="form-group">
                <label class="form-label">Сложность (1–5)</label>
                <div class="meter">
                    <input type="range" name="difficulty" id="difficulty" min="1" max="5" step="1" value="{{ old('difficulty', $expression->difficulty ?? 1) }}" style="width:100%;accent-color:var(--blue)">
                    <span id="diffNum" style="font-weight:800;color:var(--blue);min-width:20px;text-align:center">{{ old('difficulty', $expression->difficulty ?? 1) }}</span>
                </div>
            </div>

            <div class="form-group full">
                <label class="form-label">Значение (на английском)</label>
                <input type="text" name="meaning" class="form-input" value="{{ old('meaning', $expression->meaning) }}">
            </div>

            <div class="form-group full">
                <label class="form-label">Дословный перевод</label>
                <input type="text" name="literal_translation" class="form-input" value="{{ old('literal_translation', $expression->literal_translation) }}">
            </div>

            <div class="form-group full">
                <label class="form-label">Пример использования</label>
                <input type="text" name="example" class="form-input" value="{{ old('example', $expression->example) }}">
            </div>

            <div class="form-group">
                <label class="form-label">Глагол (для фразовых глаголов)</label>
                <input type="text" name="base_verb" class="form-input" value="{{ old('base_verb', $expression->base_verb) }}">
            </div>
            <div class="form-group">
                <label class="form-label">Частица (для фразовых глаголов)</label>
                <input type="text" name="particle" class="form-input" value="{{ old('particle', $expression->particle) }}">
            </div>

            <div class="form-group">
                <label class="form-label">Разделяемый (фразовый глагол)</label>
                <select name="separable" class="form-select">
                    <option value="">—</option>
                    <option value="1" {{ old('separable', $expression->separable === true ? '1' : ($expression->separable === false ? '0' : '')) === '1' ? 'selected' : '' }}>Да</option>
                    <option value="0" {{ old('separable', $expression->separable === true ? '1' : ($expression->separable === false ? '0' : '')) === '0' ? 'selected' : '' }}>Нет</option>
                </select>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                Сохранить изменения
            </button>
            <a href="{{ route('admin.vocabulary.expressions.index') }}" class="btn btn-ghost">Отмена</a>
        </div>
    </form>
@endsection

@push('scripts')
<script>
    const range = document.getElementById('difficulty'), num = document.getElementById('diffNum');
    range.addEventListener('input', () => { num.textContent = range.value; });
</script>
@endpush
