@extends('layouts.app')

@section('title', 'Tests')
@section('page_title', 'Tests')
@section('page_description', 'Check your knowledge by level')

@section('content')
    <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
        <div class="mb-8" data-reveal>
            <h1 class="text-3xl font-extrabold text-ink sm:text-4xl">Тесты</h1>
            <p class="mt-1 text-ink/60">Проверьте свои знания по уровням.</p>
        </div>

        {{-- Закреплённая карточка теста на определение уровня — намеренно
             тёмный фон вместо обычной bg-armor2 у test-card, чтобы не
             затерялась среди десятков обычных тестов ниже. --}}
        <a
            href="{{ route('tests.placement.intro') }}"
            class="card-lift mb-8 flex flex-col gap-4 overflow-hidden rounded-2xl border-2 border-brand/40 bg-navy p-6 text-white shadow-softLg sm:flex-row sm:items-center"
            data-reveal
        >
            <span class="inline-flex h-14 w-14 shrink-0 items-center justify-center rounded-xl bg-gold/15 text-gold">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M9 11l3 3L22 4" /><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11" /></svg>
            </span>
            <div class="flex-1">
                <div class="flex flex-wrap items-center gap-2">
                    <h2 class="font-display text-xl font-semibold">Определение уровня</h2>
                    @if (auth()->user()->level_id)
                        <x-ui.badge variant="level" :level="auth()->user()->level" />
                    @endif
                </div>
                <p class="mt-1 text-sm text-white/60">50 вопросов · ~20 минут · результат A1–C2</p>
            </div>
            <span class="inline-flex shrink-0 items-center gap-1.5 whitespace-nowrap text-xs font-bold uppercase tracking-wider text-gold">
                {{ auth()->user()->level_id ? 'Пройти заново' : 'Начать' }}
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="transition group-hover:translate-x-1"><path d="M5 12h14M13 5l7 7-7 7" /></svg>
            </span>
        </a>

        {{-- Быстрая неофициальная оценка по тексту (своя ML-модель, ml/) —
             второстепенная карточка: обычный светлый фон, чтобы не спорить
             с закреплённой карточкой полного теста выше. --}}
        <a
            href="{{ route('tests.level-check.index') }}"
            class="card-lift mb-8 flex flex-col gap-4 overflow-hidden rounded-2xl border border-line bg-armor2 p-6 shadow-soft sm:flex-row sm:items-center"
            data-reveal
        >
            <span class="inline-flex h-14 w-14 shrink-0 items-center justify-center rounded-xl bg-brand/10 text-brand">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M4 7V5a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v2" /><path d="M9 20h6M12 15v5" /><rect x="4" y="7" width="16" height="9" rx="1.5" /></svg>
            </span>
            <div class="flex-1">
                <h2 class="font-display text-xl font-semibold text-ink">Определи уровень по тексту</h2>
                <p class="mt-1 text-sm text-ink/60">1–5 предложений · результат за секунды · вероятности A1–C2</p>
            </div>
            <span class="inline-flex shrink-0 items-center gap-1.5 whitespace-nowrap text-xs font-bold uppercase tracking-wider text-brand">
                Попробовать
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 5l7 7-7 7" /></svg>
            </span>
        </a>

        @if ($testsByLevel->isEmpty())
            <x-ui.card :hover="false" class="py-16 text-center">
                <p class="text-ink/50">Тесты пока не опубликованы. Загляните позже!</p>
            </x-ui.card>
        @else
            @foreach ($testsByLevel as $level => $tests)
                <div class="mb-3 flex items-center gap-2" data-reveal>
                    <h2 class="text-lg font-extrabold text-ink">{{ $level }}</h2>
                    <span class="text-sm text-ink/50">{{ $tests->count() }} тестов</span>
                </div>
                <div class="mb-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                    @foreach ($tests as $test)
                        <x-test-card :test="$test" />
                    @endforeach
                </div>
            @endforeach
        @endif
    </div>
@endsection
