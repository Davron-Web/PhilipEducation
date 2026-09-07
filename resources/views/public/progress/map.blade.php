{{-- Карта прогресса: уровни, слабые и уверенные темы. --}}
@extends('layouts.app')

@section('title', 'Карта прогресса')
@section('page_title', 'Карта прогресса')

@section('content')
    <div class="mx-auto max-w-4xl px-4 py-10 sm:px-6 lg:px-8">

        <div class="mb-8">
            <h1 class="font-display text-3xl font-semibold text-ink">Карта прогресса</h1>
            <p class="mt-1 text-ink/60">Где вы уже уверенно и на что стоит потратить время.</p>
        </div>

        {{-- Уровни --}}
        <h2 class="mb-3 text-sm font-bold uppercase tracking-wider text-ink/50">Уровни</h2>

        <div class="mb-10 space-y-3">
            @foreach ($levels as $row)
                <a href="{{ route('lessons.index', ['level' => $row['level']->code]) }}" class="block rounded-2xl border border-line bg-armor2 p-5 transition hover:border-brand/30">
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-brand/10 font-bold text-brand">
                                {{ $row['level']->code }}
                            </span>
                            <div>
                                <p class="font-semibold text-ink">{{ $row['level']->name }}</p>
                                <p class="text-sm text-ink/50">
                                    Пройдено {{ $row['completed'] }} из {{ $row['total'] }}
                                    @if ($row['in_progress'] > 0)
                                        · начато {{ $row['in_progress'] }}
                                    @endif
                                </p>
                            </div>
                        </div>
                        <span class="text-xl font-extrabold text-ink" style="font-variant-numeric: tabular-nums">{{ $row['percent'] }}%</span>
                    </div>

                    <div class="mt-3 h-2 overflow-hidden rounded-full bg-surface2">
                        <div class="h-full rounded-full bg-brand" style="width: {{ $row['percent'] }}%"></div>
                    </div>
                </a>
            @endforeach
        </div>

        {{-- Темы --}}
        @if ($weakTopics->isEmpty() && $strongTopics->isEmpty())
            <x-ui.card :hover="false" class="py-12 text-center">
                <p class="font-semibold text-ink">Про темы пока сказать нечего</p>
                <p class="mx-auto mt-1 max-w-md text-sm text-ink/50">
                    Карта тем строится по ответам в тестах. Пройдите несколько —
                    и здесь появится, что даётся легко, а что стоит повторить.
                </p>
                <x-ui.button :href="route('tests.index')" class="mt-5">К тестам</x-ui.button>
            </x-ui.card>
        @else
            <div class="grid gap-6 sm:grid-cols-2">
                <div>
                    <h2 class="mb-3 text-sm font-bold uppercase tracking-wider text-red-500">Стоит повторить</h2>

                    @if ($weakTopics->isEmpty())
                        <x-ui.card :hover="false" class="text-sm text-ink/50">Слабых тем нет — так держать.</x-ui.card>
                    @else
                        <div class="divide-y divide-line overflow-hidden rounded-2xl border border-line bg-armor2">
                            @foreach ($weakTopics as $row)
                                <a href="{{ route('search', ['q' => str_replace('-', ' ', $row['topic'])]) }}" class="flex items-center justify-between gap-3 px-5 py-3 transition hover:bg-surface2">
                                    <span class="min-w-0 truncate font-semibold text-ink">{{ $labeler->label($row['topic']) }}</span>
                                    <span class="shrink-0 text-sm font-bold text-red-500" style="font-variant-numeric: tabular-nums">
                                        {{ (int) round($row['accuracy'] * 100) }}%
                                    </span>
                                </a>
                            @endforeach
                        </div>
                    @endif
                </div>

                <div>
                    <h2 class="mb-3 text-sm font-bold uppercase tracking-wider text-green-600 dark:text-green-400">Уверенно</h2>

                    @if ($strongTopics->isEmpty())
                        <x-ui.card :hover="false" class="text-sm text-ink/50">Пока рано судить — нужно больше ответов.</x-ui.card>
                    @else
                        <div class="divide-y divide-line overflow-hidden rounded-2xl border border-line bg-armor2">
                            @foreach ($strongTopics as $row)
                                <div class="flex items-center justify-between gap-3 px-5 py-3">
                                    <span class="min-w-0 truncate font-semibold text-ink">{{ $labeler->label($row['topic']) }}</span>
                                    <span class="shrink-0 text-sm font-bold text-green-600 dark:text-green-400" style="font-variant-numeric: tabular-nums">
                                        {{ (int) round($row['accuracy'] * 100) }}%
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        @endif
    </div>
@endsection
