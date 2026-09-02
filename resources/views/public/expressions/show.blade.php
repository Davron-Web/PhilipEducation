{{-- Страница выражения: значение, перевод, дословный перевод, пример,
     произношение. Зеркало слов/show.blade.php. --}}
@extends('layouts.app')

@section('title', $expression->text)
@section('page_title', 'Выражения')
@section('meta_description', "Выражение «{$expression->text}» — значение и перевод в Philip Education.")

@section('content')
    <div class="mx-auto max-w-2xl px-4 py-10 sm:px-6 lg:px-8">
        <a href="{{ route('expressions.index') }}" class="mb-6 inline-flex items-center gap-1 text-sm font-semibold text-ink/60 transition hover:text-brand">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 12H5M11 18l-6-6 6-6" /></svg>
            Все выражения
        </a>

        @php
            $types = [
                'idiom' => 'Идиома',
                'phrasal_verb' => 'Фразовый глагол',
                'proverb' => 'Пословица',
                'collocation' => 'Коллокация',
            ];
        @endphp

        <x-ui.card :hover="false">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <h1 class="flex items-center gap-2 text-3xl font-extrabold text-ink">
                        {{ $expression->text }}
                        <x-speak-button :word="$expression->text" :audio-url="$expression->audio_url" />
                    </h1>
                    @if ($expression->transcription)
                        <span class="text-ink/40">/{{ $expression->transcription }}/</span>
                    @endif

                    <div class="mt-2 flex flex-wrap items-center gap-2">
                        <x-ui.badge variant="accent">{{ $types[$expression->type] ?? $expression->type }}</x-ui.badge>
                        @if ($expression->level)
                            <x-ui.badge variant="level" :level="$expression->level" />
                        @endif
                        @if ($expression->category)
                            <span class="inline-flex w-fit items-center rounded-full bg-sun/10 border border-sun/30 px-2.5 py-0.5 text-xs font-bold text-sun">{{ $expression->category }}</span>
                        @endif
                    </div>
                </div>
                <x-ui.badge :variant="$isLearned ? 'success' : 'neutral'">{{ $isLearned ? 'Выучено' : 'Новое' }}</x-ui.badge>
            </div>

            @if ($expression->type === 'phrasal_verb' && ($expression->base_verb || $expression->particle))
                <div class="mt-6 flex flex-wrap gap-4 rounded-xl bg-brand/5 px-4 py-3 text-sm">
                    @if ($expression->base_verb)
                        <span><span class="text-ink/40">Глагол:</span> <span class="font-semibold text-ink">{{ $expression->base_verb }}</span></span>
                    @endif
                    @if ($expression->particle)
                        <span><span class="text-ink/40">Частица:</span> <span class="font-semibold text-ink">{{ $expression->particle }}</span></span>
                    @endif
                    @if (! is_null($expression->separable))
                        <span><span class="text-ink/40">Разделяемый:</span> <span class="font-semibold text-ink">{{ $expression->separable ? 'да' : 'нет' }}</span></span>
                    @endif
                </div>
            @endif

            @if ($expression->meaning)
                <div class="mt-6">
                    <h2 class="mb-2 text-sm font-bold uppercase tracking-wide text-ink/40">Значение (на английском)</h2>
                    <p class="text-ink/70">{{ $expression->meaning }}</p>
                </div>
            @endif

            @if ($expression->translations->isNotEmpty())
                <div class="mt-6">
                    <h2 class="mb-2 text-sm font-bold uppercase tracking-wide text-ink/40">Перевод</h2>
                    <ul class="space-y-2">
                        @foreach ($expression->translations as $translation)
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

            @if ($expression->literal_translation)
                <div class="mt-6">
                    <h2 class="mb-2 text-sm font-bold uppercase tracking-wide text-ink/40">Дословный перевод</h2>
                    <p class="rounded-xl bg-ink/5 px-4 py-3 text-ink/60">{{ $expression->literal_translation }}</p>
                </div>
            @endif

            @if ($expression->example)
                <div class="mt-6">
                    <h2 class="mb-2 text-sm font-bold uppercase tracking-wide text-ink/40">Пример</h2>
                    <p class="rounded-xl bg-ink/5 px-4 py-3 italic text-ink/70">&laquo;{{ $expression->example }}&raquo;</p>
                </div>
            @endif
        </x-ui.card>
    </div>
@endsection
