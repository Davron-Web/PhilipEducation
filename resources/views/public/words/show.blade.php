{{-- Страница слова: перевод, пример, произношение. Без фото. --}}
@extends('layouts.app')

@section('title', $word->word)
@section('page_title', 'Словарь')
@section('meta_description', "Слово «{$word->word}» — перевод и примеры в Philip Education.")

@section('content')
    <div class="mx-auto max-w-2xl px-4 py-10 sm:px-6 lg:px-8">
        <a href="{{ route('words.index') }}" class="mb-6 inline-flex items-center gap-1 text-sm font-semibold text-ink/60 transition hover:text-brand">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 12H5M11 18l-6-6 6-6" /></svg>
            Все слова
        </a>

        <x-ui.card :hover="false">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <h1 class="flex items-center gap-2 text-3xl font-extrabold capitalize text-ink">
                        {{ $word->word }}
                        <x-speak-button :word="$word->word" :audio-url="$word->audio_url" />
                    </h1>
                    @if ($word->transcription)
                        <span class="text-ink/40">/{{ $word->transcription }}/</span>
                    @endif
                    @if ($word->category)
                        <span class="mt-1 block w-fit rounded-full bg-sun/10 border border-sun/30 px-2.5 py-0.5 text-xs font-bold text-sun">{{ $word->category }}</span>
                    @endif
                </div>
                <x-ui.badge :variant="$isLearned ? 'success' : 'neutral'">{{ $isLearned ? 'Выучено' : 'Новое' }}</x-ui.badge>
            </div>

            @if ($word->translations->isNotEmpty())
                <div class="mt-6">
                    <h2 class="mb-2 text-sm font-bold uppercase tracking-wide text-ink/40">Перевод</h2>
                    <ul class="space-y-2">
                        @foreach ($word->translations as $translation)
                            <li class="flex items-center justify-between rounded-xl bg-brand/5 px-4 py-2.5">
                                <span class="font-semibold text-ink">{{ $translation->translation }}</span>
                                <span class="text-xs font-bold uppercase text-ink/40">{{ $translation->language }}</span>
                            </li>
                            @if ($translation->definition)
                                <li class="px-4 text-sm text-ink/50">{{ $translation->definition }}</li>
                            @endif
                        @endforeach
                    </ul>
                </div>
            @endif

            @if ($word->example)
                <div class="mt-6">
                    <h2 class="mb-2 text-sm font-bold uppercase tracking-wide text-ink/40">Пример</h2>
                    <p class="rounded-xl bg-ink/5 px-4 py-3 italic text-ink/70">&laquo;{{ $word->example }}&raquo;</p>
                </div>
            @endif

            @if ($word->lesson)
                <x-ui.button href="{{ route('lessons.show', $word->lesson->id) }}" variant="outline" class="mt-6">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20M4 19.5A2.5 2.5 0 0 0 6.5 22H20V4H6.5A2.5 2.5 0 0 0 4 6.5v13Z" /></svg>
                    Из урока: {{ $word->lesson->title }}
                </x-ui.button>
            @endif
        </x-ui.card>
    </div>
@endsection
