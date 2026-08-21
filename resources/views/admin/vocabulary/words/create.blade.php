@extends('layouts.admin')

@section('title', 'Добавить слово')

@section('content')
    @php
        $lessons = $lessons ?? \App\Models\Content\Lesson::orderBy('order_number')->get();
    @endphp

    <x-admin.page-header title="Добавить слово" :backRoute="route('admin.vocabulary.words.index')" />

    <form action="{{ route('admin.vocabulary.words.store') }}" method="POST" class="card">
        @csrf
        <div class="form-grid">
            <div class="form-group">
                <label class="form-label">Слово *</label>
                <input type="text" name="word" class="form-input" placeholder="Например: apple" value="{{ old('word') }}" required>
            </div>
            <div class="form-group">
                <label class="form-label">Транскрипция</label>
                <input type="text" name="transcription" class="form-input" placeholder="ˈæpl" value="{{ old('transcription') }}">
            </div>

            <div class="form-group">
                <label class="form-label">Урок *</label>
                <select name="lesson_id" class="form-select" required>
                    <option value="" disabled {{ old('lesson_id') ? '' : 'selected' }}>Выберите урок</option>
                    @foreach($lessons as $lesson)
                        <option value="{{ $lesson->id }}" {{ old('lesson_id') == $lesson->id ? 'selected' : '' }}>{{ $lesson->title }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Сложность (1–5)</label>
                <div class="meter">
                    <input type="range" name="difficulty" id="difficulty" min="1" max="5" step="1" value="{{ old('difficulty', 1) }}" style="width:100%;accent-color:var(--blue)">
                    <span id="diffNum" style="font-weight:800;color:var(--blue);min-width:20px;text-align:center">{{ old('difficulty', 1) }}</span>
                </div>
            </div>

            <div class="form-group full">
                <label class="form-label">Пример использования</label>
                <input type="text" name="example" class="form-input" placeholder="I eat an apple every day." value="{{ old('example') }}">
            </div>

            <div class="form-group full">
                <label class="form-label">URL изображения</label>
                <input type="text" name="image" id="imageUrl" class="form-input" placeholder="https://example.com/apple.jpg" value="{{ old('image') }}">
                <div style="display:flex;gap:14px;align-items:center;margin-top:10px">
                    <img id="imgPreview" class="media-thumb" style="display:none" src="" alt="Превью">
                    <div id="imgEmpty" class="media-thumb-empty">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                    </div>
                </div>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                Сохранить слово
            </button>
            <a href="{{ route('admin.vocabulary.words.index') }}" class="btn btn-ghost">Отмена</a>
        </div>
    </form>
@endsection

@push('scripts')
<script>
    const urlInput = document.getElementById('imageUrl'), img = document.getElementById('imgPreview'), empty = document.getElementById('imgEmpty');
    function refreshPreview() {
        const v = (urlInput.value || '').trim();
        if (v) { img.src = v; img.style.display = 'block'; empty.style.display = 'none'; }
        else { img.style.display = 'none'; empty.style.display = 'grid'; }
    }
    img.onerror = () => { img.style.display = 'none'; empty.style.display = 'grid'; };
    urlInput.addEventListener('input', refreshPreview);
    refreshPreview();

    const range = document.getElementById('difficulty'), num = document.getElementById('diffNum');
    range.addEventListener('input', () => { num.textContent = range.value; });
</script>
@endpush
