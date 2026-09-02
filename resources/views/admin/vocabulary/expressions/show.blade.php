@extends('layouts.admin')

@section('title', 'Выражение: ' . $expression->text)

@section('content')
    @php
        $diff = (int) ($expression->difficulty ?? 0);
        $diffClass = $diff <= 2 ? 'easy' : ($diff == 3 ? 'medium' : 'hard');
        $diffLabel = $diff <= 2 ? 'Легко' : ($diff == 3 ? 'Средне' : 'Сложно');
        $types = [
            'idiom' => 'Идиома',
            'phrasal_verb' => 'Фразовый глагол',
            'proverb' => 'Пословица',
            'collocation' => 'Коллокация',
        ];
    @endphp

    <x-admin.page-header :title="'Выражение: ' . $expression->text" :backRoute="route('admin.vocabulary.expressions.index')" />

    <div class="show-grid">
        <div class="card icon-card">
            <div class="media-preview-empty">
                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
            </div>
            <div>
                <div class="icon-card-title">{{ $expression->text }} <x-speak-button :word="$expression->text" :audio-url="$expression->audio_url" /></div>
                @if($expression->transcription)
                    <div style="color:var(--deep);font-family:'SF Mono',Consolas,monospace;font-size:15px;font-weight:600;margin-top:4px">[{{ $expression->transcription }}]</div>
                @endif
            </div>
            <span class="badge {{ $diffClass }}">{{ $diff }} / 5 · {{ $diffLabel }}</span>
        </div>

        <div class="card">
            <h3>Информация о выражении</h3>
            <div class="info-row"><span class="info-label">ID</span><span class="info-value">#{{ $expression->id }}</span></div>
            <div class="info-row"><span class="info-label">Тип</span><span class="info-value">{{ $types[$expression->type] ?? $expression->type }}</span></div>
            <div class="info-row">
                <span class="info-label">Уровень</span>
                <span class="info-value">{{ $expression->level->code ?? '—' }}</span>
            </div>
            <div class="info-row"><span class="info-label">Категория</span><span class="info-value">{{ $expression->category ?: '—' }}</span></div>
            <div class="info-row">
                <span class="info-label">Сложность</span>
                <span class="info-value">
                    <div class="meter">
                        <div class="meter-track"><span class="meter-fill" style="width: {{ min(100, max(0, $diff * 20)) }}%"></span></div>
                        {{ $diff }}/5
                    </div>
                </span>
            </div>

            @if($expression->type === 'phrasal_verb')
                <div class="info-row"><span class="info-label">Глагол</span><span class="info-value">{{ $expression->base_verb ?: '—' }}</span></div>
                <div class="info-row"><span class="info-label">Частица</span><span class="info-value">{{ $expression->particle ?: '—' }}</span></div>
                <div class="info-row"><span class="info-label">Разделяемый</span><span class="info-value">{{ is_null($expression->separable) ? '—' : ($expression->separable ? 'Да' : 'Нет') }}</span></div>
            @endif

            @if($expression->meaning)
                <div class="info-row"><span class="info-label">Значение</span><span class="info-value" style="font-style:italic">{{ $expression->meaning }}</span></div>
            @endif
            @if($expression->literal_translation)
                <div class="info-row"><span class="info-label">Дословный перевод</span><span class="info-value">{{ $expression->literal_translation }}</span></div>
            @endif
            @if($expression->example)
                <div class="info-row"><span class="info-label">Пример</span><span class="info-value" style="font-style:italic">{{ $expression->example }}</span></div>
            @endif
            <div class="info-row"><span class="info-label">Создано</span><span class="info-value">{{ optional($expression->created_at)->format('d.m.Y H:i') ?? '—' }}</span></div>
            <div class="info-row"><span class="info-label">Обновлено</span><span class="info-value">{{ optional($expression->updated_at)->format('d.m.Y H:i') ?? '—' }}</span></div>

            @if($expression->translations && $expression->translations->count())
                <h3 style="margin-top:20px">Переводы</h3>
                <div style="display:flex;flex-direction:column;gap:8px">
                    @foreach($expression->translations as $t)
                        <div style="display:flex;justify-content:space-between;align-items:center;gap:12px;background:var(--bg);border:1px solid var(--border);border-radius:10px;padding:10px 14px;font-size:13.5px">
                            <span style="color:var(--deep);font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;padding:3px 8px;border-radius:6px;background:rgba(0,71,255,.08)">{{ $t->language ?? 'RU' }}</span>
                            <b>{{ $t->translation }}</b>
                        </div>
                    @endforeach
                </div>
            @endif

            <div class="form-actions" style="margin-top:16px">
                <a href="{{ route('admin.vocabulary.expressions.edit', $expression) }}" class="btn btn-warning">Изменить</a>
                <form action="{{ route('admin.vocabulary.expressions.destroy', $expression) }}" method="POST" onsubmit="return confirm('Удалить выражение «{{ addslashes($expression->text) }}»?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Удалить</button>
                </form>
            </div>
        </div>
    </div>
@endsection
