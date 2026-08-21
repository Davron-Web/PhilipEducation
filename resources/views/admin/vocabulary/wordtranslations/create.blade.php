@extends('layouts.admin')

@section('title', 'Добавить перевод слова')

@section('content')
    <x-admin.page-header title="Добавить перевод слова" :backRoute="route('admin.vocabulary.wordtranslations.index')" />

    <form action="{{ route('admin.vocabulary.wordtranslations.store') }}" method="POST" class="card">
        @csrf
        <div class="form-grid">
            <div class="form-group full">
                <label class="form-label">Слово *</label>
                <select name="word_id" class="form-select @error('word_id') is-invalid @enderror" required>
                    <option value="">Выберите слово</option>
                    @foreach($words as $word)
                        <option value="{{ $word->id }}" {{ old('word_id') == $word->id ? 'selected' : '' }}>{{ $word->word }}</option>
                    @endforeach
                </select>
                @error('word_id')<div class="error-text">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label class="form-label">Язык *</label>
                <input type="text" name="language" class="form-input @error('language') is-invalid @enderror" value="{{ old('language') }}" maxlength="10" placeholder="ru, en, de..." required>
                @error('language')<div class="error-text">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label class="form-label">Перевод *</label>
                <input type="text" name="translation" class="form-input @error('translation') is-invalid @enderror" value="{{ old('translation') }}" required>
                @error('translation')<div class="error-text">{{ $message }}</div>@enderror
            </div>
            <div class="form-group full">
                <label class="form-label">Определение</label>
                <textarea name="definition" class="form-textarea" rows="3">{{ old('definition') }}</textarea>
            </div>
            <div class="form-group full">
                <label class="form-label">Пример</label>
                <textarea name="example" class="form-textarea" rows="3">{{ old('example') }}</textarea>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Создать</button>
            <a href="{{ route('admin.vocabulary.wordtranslations.index') }}" class="btn btn-ghost">Отмена</a>
        </div>
    </form>
@endsection
