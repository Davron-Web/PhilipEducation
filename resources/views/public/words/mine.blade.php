{{-- Личный словарь: только слова, которые ученик учит или добавил сам. --}}
@extends('layouts.app')

@section('title', 'Мой словарь')
@section('page_title', 'Мой словарь')

@php
    $filters = [
        'all' => 'Все',
        'learning' => 'Учу',
        'learned' => 'Выучено',
        'own' => 'Мои слова',
    ];
@endphp

@section('content')
    <div class="mx-auto max-w-4xl px-4 py-10 sm:px-6 lg:px-8">

        <a href="{{ route('words.index') }}" class="mb-4 inline-flex items-center gap-1 text-sm font-semibold text-ink/60 transition hover:text-brand">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 12H5M11 18l-6-6 6-6" /></svg>
            Весь словарь
        </a>

        <div class="mb-6 flex flex-wrap items-end justify-between gap-4">
            <div>
                <h1 class="font-display text-3xl font-semibold text-ink">Мой словарь</h1>
                <p class="mt-1 text-ink/60">{{ $counts['all'] }} слов, из них выучено {{ $counts['learned'] }}.</p>
            </div>

            @if ($counts['learning'] > 0)
                <x-ui.button :href="route('words.review')">Повторить слова</x-ui.button>
            @endif
        </div>

        <div class="mb-6 flex flex-wrap gap-2">
            @foreach ($filters as $key => $label)
                <a
                    href="{{ route('words.mine', $key === 'all' ? [] : ['filter' => $key]) }}"
                    class="rounded-full px-4 py-1.5 text-sm font-bold transition {{ $filter === $key ? 'bg-brand text-white shadow-md shadow-brand/25' : 'bg-armor2 text-ink/60 hover:bg-surface2' }}"
                >{{ $label }} <span class="opacity-60">{{ $counts[$key] }}</span></a>
            @endforeach
        </div>

        @if ($words->isEmpty())
            <x-ui.card :hover="false" class="py-16 text-center">
                <p class="text-lg font-semibold text-ink">
                    {{ $filter === 'all' ? 'В словаре пока пусто' : 'Здесь пока пусто' }}
                </p>
                <p class="mx-auto mt-1 max-w-md text-sm text-ink/50">
                    Отмечайте слова «Знаю» в разделе «Словарь» или добавляйте свои — они появятся здесь.
                </p>
                <x-ui.button :href="route('words.index')" class="mt-5">Перейти в словарь</x-ui.button>
            </x-ui.card>
        @else
            <div class="divide-y divide-line overflow-hidden rounded-2xl border border-line bg-armor2">
                @foreach ($words as $word)
                    <div class="flex items-center gap-4 px-5 py-3.5">
                        <div class="min-w-0 flex-1">
                            <a href="{{ route('words.show', $word->id) }}" class="flex flex-wrap items-baseline gap-2 font-semibold text-ink hover:text-brand">
                                {{ $word->word }}
                                @if ($word->transcription)
                                    <span class="text-sm font-normal text-ink/40">{{ $word->transcription }}</span>
                                @endif
                            </a>
                            <p class="truncate text-sm text-ink/60">{{ $word->translations->first()?->translation ?? '—' }}</p>
                        </div>

                        @if (is_null($word->lesson_id))
                            <span class="shrink-0 rounded-full bg-sky/10 px-2.5 py-0.5 text-xs font-bold text-sky">своё</span>
                        @endif

                        <span class="shrink-0 rounded-full px-2.5 py-0.5 text-xs font-bold {{ $word->pivot->learned ? 'bg-green-500/10 text-green-600 dark:text-green-400' : 'bg-sun/10 text-sun' }}">
                            {{ $word->pivot->learned ? 'выучено' : 'учу' }}
                        </span>
                    </div>
                @endforeach
            </div>

            @if ($words->hasPages())
                <div class="mt-6">{{ $words->links() }}</div>
            @endif
        @endif
    </div>
@endsection
