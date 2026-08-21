@extends('layouts.admin')

@section('title', 'Перевод: ' . $wordTranslation->translation)

@section('content')
    <x-admin.page-header :title="'Перевод: ' . $wordTranslation->translation" :backRoute="route('admin.vocabulary.wordtranslations.index')">
        <x-slot:actions>
            <a href="{{ route('admin.vocabulary.wordtranslations.edit', $wordTranslation) }}" class="btn btn-warning">Изменить</a>
        </x-slot:actions>
    </x-admin.page-header>

    <div class="card">
        <h3>Информация</h3>
        <div class="info-row"><span class="info-label">ID</span><span class="info-value">#{{ $wordTranslation->id }}</span></div>
        <div class="info-row">
            <span class="info-label">Слово</span>
            <span class="info-value">
                @if($wordTranslation->word)
                    <a class="chip-link" href="{{ route('admin.vocabulary.words.show', $wordTranslation->word) }}">{{ $wordTranslation->word->word }}</a>
                @else
                    —
                @endif
            </span>
        </div>
        <div class="info-row"><span class="info-label">Язык</span><span class="info-value"><span class="badge badge-primary">{{ strtoupper($wordTranslation->language) }}</span></span></div>
        <div class="info-row"><span class="info-label">Перевод</span><span class="info-value" style="font-weight:700">{{ $wordTranslation->translation }}</span></div>
        <div class="info-row"><span class="info-label">Определение</span><span class="info-value">{{ $wordTranslation->definition ?? '—' }}</span></div>
        <div class="info-row"><span class="info-label">Пример</span><span class="info-value">{{ $wordTranslation->example ?? '—' }}</span></div>
        <div class="info-row"><span class="info-label">Создано</span><span class="info-value">{{ optional($wordTranslation->created_at)->format('d.m.Y H:i') }}</span></div>
        <div class="info-row"><span class="info-label">Обновлено</span><span class="info-value">{{ optional($wordTranslation->updated_at)->format('d.m.Y H:i') }}</span></div>

        <div class="form-actions" style="margin-top:16px">
            <form action="{{ route('admin.vocabulary.wordtranslations.destroy', $wordTranslation) }}" method="POST" onsubmit="return confirm('Удалить перевод?');">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-danger">Удалить перевод</button>
            </form>
        </div>
    </div>
@endsection
