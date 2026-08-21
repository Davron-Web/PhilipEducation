@extends('layouts.admin')

@section('title', 'Запись #' . $userWord->id)

@section('content')
    @php
        $total = $userWord->correct_answers + $userWord->wrong_answers;
        $accuracy = $total > 0 ? round(($userWord->correct_answers / $total) * 100, 1) : 0;
        $accColor = $accuracy >= 80 ? 'var(--green)' : ($accuracy >= 50 ? '#A16207' : 'var(--red)');
    @endphp

    <x-admin.page-header :title="'Запись #' . $userWord->id" :backRoute="route('admin.user.userwords.index')">
        <x-slot:badge>
            @if($userWord->learned)
                <span class="badge badge-success">Выучено</span>
            @else
                <span class="badge badge-warning">В процессе</span>
            @endif
        </x-slot:badge>
        <x-slot:actions>
            <a href="{{ route('admin.user.userwords.edit', $userWord) }}" class="btn btn-warning">Изменить</a>
        </x-slot:actions>
    </x-admin.page-header>

    <div class="card">
        <h3>Информация</h3>
        <div class="info-row"><span class="info-label">ID</span><span class="info-value">#{{ $userWord->id }}</span></div>
        <div class="info-row">
            <span class="info-label">Пользователь</span>
            <span class="info-value">
                @if($userWord->user)
                    <a class="chip-link" href="{{ route('admin.user.users.show', $userWord->user) }}">{{ $userWord->user->name ?? $userWord->user->email }}</a>
                @else
                    —
                @endif
            </span>
        </div>
        <div class="info-row">
            <span class="info-label">Слово</span>
            <span class="info-value">
                @if($userWord->word)
                    <a class="chip-link" href="{{ route('admin.vocabulary.words.show', $userWord->word) }}">{{ $userWord->word->word }}</a>
                @else
                    —
                @endif
            </span>
        </div>
        <div class="info-row"><span class="info-label">Транскрипция</span><span class="info-value">{{ $userWord->word?->transcription ?? '—' }}</span></div>
        <div class="info-row"><span class="info-label">Правильные ответы</span><span class="info-value" style="color:var(--green)">{{ $userWord->correct_answers }}</span></div>
        <div class="info-row"><span class="info-label">Неправильные ответы</span><span class="info-value" style="color:var(--red)">{{ $userWord->wrong_answers }}</span></div>
        <div class="info-row"><span class="info-label">Всего попыток</span><span class="info-value">{{ $total }}</span></div>
        <div class="info-row">
            <span class="info-label">Точность</span>
            <span class="info-value">
                <div class="meter">
                    <div class="meter-track"><span class="meter-fill" style="width:{{ $accuracy }}%;background:{{ $accColor }}"></span></div>
                    {{ $accuracy }}%
                </div>
            </span>
        </div>
        <div class="info-row"><span class="info-label">Последний повтор</span><span class="info-value">{{ optional($userWord->last_reviewed_at)->format('d.m.Y H:i') ?? 'Никогда' }}</span></div>

        <div class="form-actions" style="margin-top:16px">
            <form action="{{ route('admin.user.userwords.destroy', $userWord) }}" method="POST" onsubmit="return confirm('Удалить запись?');">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-danger">Удалить запись</button>
            </form>
        </div>
    </div>
@endsection
