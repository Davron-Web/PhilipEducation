@extends('layouts.admin')

@section('title', 'Слово: ' . $word->word)

@section('content')
    @php
        $diff = (int) ($word->difficulty ?? 0);
        $diffClass = $diff <= 2 ? 'easy' : ($diff == 3 ? 'medium' : 'hard');
        $diffLabel = $diff <= 2 ? 'Легко' : ($diff == 3 ? 'Средне' : 'Сложно');
    @endphp

    <x-admin.page-header :title="'Слово: ' . $word->word" :backRoute="route('admin.vocabulary.words.index')" />

    <div class="show-grid">
        <div class="card icon-card">
            @if($word->image)
                <img src="{{ $word->image }}" alt="{{ $word->word }}" class="media-preview">
            @else
                <div class="media-preview-empty">
                    <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                </div>
            @endif
            <div>
                <div class="icon-card-title">{{ $word->word }} <x-speak-button :word="$word->word" :audio-url="$word->audio_url" /></div>
                @if($word->transcription)
                    <div style="color:var(--deep);font-family:'SF Mono',Consolas,monospace;font-size:15px;font-weight:600;margin-top:4px">[{{ $word->transcription }}]</div>
                @endif
            </div>
            <span class="badge {{ $diffClass }}">{{ $diff }} / 5 · {{ $diffLabel }}</span>
        </div>

        <div class="card">
            <h3>Информация о слове</h3>
            <div class="info-row"><span class="info-label">ID</span><span class="info-value">#{{ $word->id }}</span></div>
            <div class="info-row">
                <span class="info-label">Урок</span>
                <span class="info-value">
                    @if($word->lesson)
                        <a class="chip-link" href="{{ route('admin.content.lessons.show', $word->lesson) }}">{{ $word->lesson->title }}</a>
                    @else
                        —
                    @endif
                </span>
            </div>
            <div class="info-row">
                <span class="info-label">Сложность</span>
                <span class="info-value">
                    <div class="meter">
                        <div class="meter-track"><span class="meter-fill" style="width: {{ min(100, max(0, $diff * 20)) }}%"></span></div>
                        {{ $diff }}/5
                    </div>
                </span>
            </div>
            @if($word->example)
                <div class="info-row"><span class="info-label">Пример</span><span class="info-value" style="font-style:italic">{{ $word->example }}</span></div>
            @endif
            <div class="info-row"><span class="info-label">Изображение</span><span class="info-value">{{ $word->image ? '✓ Есть' : '✗ Нет' }}</span></div>
            <div class="info-row"><span class="info-label">Создано</span><span class="info-value">{{ optional($word->created_at)->format('d.m.Y H:i') ?? '—' }}</span></div>
            <div class="info-row"><span class="info-label">Обновлено</span><span class="info-value">{{ optional($word->updated_at)->format('d.m.Y H:i') ?? '—' }}</span></div>

            @if($word->translations && $word->translations->count())
                <h3 style="margin-top:20px">Переводы</h3>
                <div style="display:flex;flex-direction:column;gap:8px">
                    @foreach($word->translations as $t)
                        <div style="display:flex;justify-content:space-between;align-items:center;gap:12px;background:var(--bg);border:1px solid var(--border);border-radius:10px;padding:10px 14px;font-size:13.5px">
                            <span style="color:var(--deep);font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;padding:3px 8px;border-radius:6px;background:rgba(0,71,255,.08)">{{ $t->language ?? 'RU' }}</span>
                            <b>{{ $t->translation }}</b>
                        </div>
                    @endforeach
                </div>
            @endif

            <div class="form-actions" style="margin-top:16px">
                <a href="{{ route('admin.vocabulary.words.edit', $word) }}" class="btn btn-warning">Изменить</a>
                <form action="{{ route('admin.vocabulary.words.destroy', $word) }}" method="POST" onsubmit="return confirm('Удалить слово «{{ addslashes($word->word) }}»?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Удалить</button>
                </form>
            </div>
        </div>
    </div>
@endsection
