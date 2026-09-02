@extends('layouts.admin')

@section('title', 'Добавить выражение')

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

    <x-admin.page-header title="Добавить выражение" :backRoute="route('admin.vocabulary.expressions.index')" />

    <form action="{{ route('admin.vocabulary.expressions.store') }}" method="POST" class="card">
        @csrf
        <div class="form-grid">
            <div class="form-group">
                <label class="form-label">Выражение *</label>
                <input type="text" name="text" class="form-input" placeholder="Например: break the ice" value="{{ old('text') }}" required>
            </div>
            <div class="form-group">
                <label class="form-label">Тип *</label>
                <select name="type" class="form-select" required>
                    @foreach($types as $key => $label)
                        <option value="{{ $key }}" {{ old('type') === $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">Транскрипция</label>
                <input type="text" name="transcription" class="form-input" value="{{ old('transcription') }}">
            </div>
            <div class="form-group">
                <label class="form-label">Уровень</label>
                <select name="level_id" class="form-select">
                    <option value="">Без уровня</option>
                    @foreach($levels as $level)
                        <option value="{{ $level->id }}" {{ old('level_id') == $level->id ? 'selected' : '' }}>{{ $level->code }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">Категория</label>
                <input type="text" name="category" class="form-input" placeholder="Например: Идиомы" value="{{ old('category') }}">
            </div>
            <div class="form-group">
                <label class="form-label">Сложность (1–5)</label>
                <div class="meter">
                    <input type="range" name="difficulty" id="difficulty" min="1" max="5" step="1" value="{{ old('difficulty', 1) }}" style="width:100%;accent-color:var(--blue)">
                    <span id="diffNum" style="font-weight:800;color:var(--blue);min-width:20px;text-align:center">{{ old('difficulty', 1) }}</span>
                </div>
            </div>

            <div class="form-group full">
                <label class="form-label">Значение (на английском)</label>
                <input type="text" name="meaning" class="form-input" placeholder="To relieve tension in an awkward situation." value="{{ old('meaning') }}">
            </div>

            <div class="form-group full">
                <label class="form-label">Дословный перевод</label>
                <input type="text" name="literal_translation" class="form-input" placeholder="разбить лёд" value="{{ old('literal_translation') }}">
            </div>

            <div class="form-group full">
                <label class="form-label">Пример использования</label>
                <input type="text" name="example" class="form-input" placeholder="He told a joke to break the ice." value="{{ old('example') }}">
            </div>

            <div class="form-group">
                <label class="form-label">Глагол (для фразовых глаголов)</label>
                <input type="text" name="base_verb" class="form-input" placeholder="turn" value="{{ old('base_verb') }}">
            </div>
            <div class="form-group">
                <label class="form-label">Частица (для фразовых глаголов)</label>
                <input type="text" name="particle" class="form-input" placeholder="off" value="{{ old('particle') }}">
            </div>

            <div class="form-group">
                <label class="form-label">Разделяемый (фразовый глагол)</label>
                <select name="separable" class="form-select">
                    <option value="">—</option>
                    <option value="1" {{ old('separable') === '1' ? 'selected' : '' }}>Да</option>
                    <option value="0" {{ old('separable') === '0' ? 'selected' : '' }}>Нет</option>
                </select>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                Сохранить выражение
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
