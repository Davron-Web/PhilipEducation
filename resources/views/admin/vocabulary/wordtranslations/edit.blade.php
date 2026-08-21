@extends('layouts.admin')

@section('title', 'Редактировать перевод')

@section('content')
    <x-admin.page-header :title="'Редактировать перевод: ' . $wordTranslation->translation" :backRoute="route('admin.vocabulary.wordtranslations.index')" />

    <form action="{{ route('admin.vocabulary.wordtranslations.update', $wordTranslation) }}" method="POST" class="card">
        @csrf
        @method('PUT')
        <div class="form-grid">
            <div class="form-group full">
                <label class="form-label">Слово *</label>
                <select name="word_id" class="form-select @error('word_id') is-invalid @enderror" required>
                    <option value="">Выберите слово</option>
                    @foreach($words as $word)
                        <option value="{{ $word->id }}" {{ old('word_id', $wordTranslation->word_id) == $word->id ? 'selected' : '' }}>{{ $word->word }}</option>
                    @endforeach
                </select>
                @error('word_id')<div class="error-text">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label class="form-label">Язык *</label>
                <input type="text" name="language" class="form-input @error('language') is-invalid @enderror" value="{{ old('language', $wordTranslation->language) }}" maxlength="10" required>
                @error('language')<div class="error-text">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label class="form-label">Перевод *</label>
                <input type="text" name="translation" class="form-input @error('translation') is-invalid @enderror" value="{{ old('translation', $wordTranslation->translation) }}" required>
                @error('translation')<div class="error-text">{{ $message }}</div>@enderror
            </div>
            <div class="form-group full">
                <label class="form-label">Определение</label>
                <textarea name="definition" class="form-textarea" rows="3">{{ old('definition', $wordTranslation->definition) }}</textarea>
            </div>
            <div class="form-group full">
                <label class="form-label">Пример</label>
                <textarea name="example" class="form-textarea" rows="3">{{ old('example', $wordTranslation->example) }}</textarea>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Сохранить изменения</button>
            <a href="{{ route('admin.vocabulary.wordtranslations.index') }}" class="btn btn-ghost">Отмена</a>
        </div>
    </form>
@endsection
