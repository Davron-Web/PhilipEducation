{{-- IELTS Writing: Task 1 (график) и Task 2 (эссе) отдельными секциями. --}}
@extends('layouts.app')

@section('title', 'IELTS')
@section('page_title', 'IELTS Writing')
@section('meta_description', 'Тренируйтесь писать IELTS Writing Task 1 (описание графика) и Task 2 (эссе) с проверкой от ИИ.')

@section('content')
    <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
        <div class="mb-10" data-reveal>
            <h1 class="text-3xl font-extrabold text-ink sm:text-4xl">IELTS Writing</h1>
            <p class="mt-1 text-ink/60">Пишите ответы прямо на сайте — Phil (Gemini) выставит band score и разберёт ошибки по критериям IELTS.</p>
        </div>

        {{-- Связующие слова: ссылка на подборку + короткое правило про Lexical Resource --}}
        <div class="mb-10 grid gap-5 lg:grid-cols-2" data-reveal>
            <a
                href="{{ route('expressions.index', ['category' => 'Связующие слова']) }}"
                class="card-lift group flex flex-col justify-between gap-4 rounded-2xl border border-line bg-navy p-6 shadow-soft sm:flex-row sm:items-center"
            >
                <div class="flex items-start gap-4">
                    <span class="inline-flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-gold/15 text-gold">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M9 17H4a2 2 0 0 1-2-2V9a2 2 0 0 1 2-2h5" /><path d="M15 17h5a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2h-5" /><path d="M9 12h6" /></svg>
                    </span>
                    <div>
                        <h2 class="font-display text-lg font-semibold text-white">{{ __('site.ielts_writing.linking_cta_title') }}</h2>
                        <p class="mt-1 text-sm text-white/60">{{ __('site.ielts_writing.linking_cta_text') }}</p>
                    </div>
                </div>
                <span class="inline-flex shrink-0 items-center gap-1.5 whitespace-nowrap text-xs font-bold uppercase tracking-wider text-gold">
                    {{ __('site.ielts_writing.linking_cta_button') }}
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="transition group-hover:translate-x-1"><path d="M5 12h14M13 5l7 7-7 7" /></svg>
                </span>
            </a>

            <div class="rounded-2xl border border-line bg-armor2/70 p-6 shadow-soft">
                <h2 class="text-sm font-bold uppercase tracking-wide text-ink/50">{{ __('site.ielts_writing.linking_rule_title') }}</h2>
                <p class="mt-2 flex items-start gap-2 text-sm text-ink/70">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" class="mt-0.5 shrink-0 text-green-600 dark:text-green-400"><path d="M20 6 9 17l-5-5" /></svg>
                    {{ __('site.ielts_writing.linking_rule_good') }}
                </p>
                <p class="mt-2 flex items-start gap-2 text-sm text-ink/70">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" class="mt-0.5 shrink-0 text-red-500"><path d="M18 6 6 18M6 6l12 12" /></svg>
                    {{ __('site.ielts_writing.linking_rule_bad') }}
                </p>
            </div>
        </div>

        <div class="mb-4" data-reveal>
            <h2 class="text-xl font-extrabold text-ink">Task 1 — описание графика</h2>
            <p class="text-sm text-ink/50">Минимум 150 слов. Опишите данные графика/таблицы своими словами.</p>
        </div>
        <div class="mb-10 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($task1 as $task)
                <a
                    href="{{ route('ielts.writing.show', $task) }}"
                    class="group flex h-full flex-col rounded-2xl border border-line bg-armor2/70 p-5 shadow-lg backdrop-blur-xl transition duration-300 ease-out hover:-translate-y-1.5 hover:border-sky/40 hover:shadow-2xl hover:shadow-sky/15"
                    data-reveal
                >
                    <div class="mb-2 flex items-center justify-between gap-2">
                        <span class="inline-flex items-center gap-1 rounded-full bg-sky/10 px-2.5 py-0.5 text-xs font-bold uppercase text-sky">{{ strtoupper($task->chart_type) }}</span>
                        @if ($bestScores->has($task->id))
                            <span class="inline-flex items-center gap-1 rounded-full bg-sun/15 px-2.5 py-0.5 text-xs font-bold text-sun">Band {{ number_format($bestScores[$task->id], 1) }}</span>
                        @endif
                    </div>
                    <h3 class="text-lg font-bold text-ink">{{ $task->title }}</h3>
                    <p class="mt-2 line-clamp-3 flex-1 text-sm text-ink/60">{{ $task->prompt }}</p>
                    <span class="mt-4 inline-flex items-center gap-1 text-sm font-bold text-sky">
                        {{ $bestScores->has($task->id) ? 'Попробовать снова' : 'Начать' }}
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" class="transition-transform group-hover:translate-x-1"><path d="M5 12h14M13 6l6 6-6 6" /></svg>
                    </span>
                </a>
            @endforeach
        </div>

        <div class="mb-4" data-reveal>
            <h2 class="text-xl font-extrabold text-ink">Task 2 — эссе-рассуждение</h2>
            <p class="text-sm text-ink/50">Минимум 250 слов. Раскройте тему, аргументируйте позицию.</p>
        </div>
        <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($task2 as $task)
                <a
                    href="{{ route('ielts.writing.show', $task) }}"
                    class="group flex h-full flex-col rounded-2xl border border-line bg-armor2/70 p-5 shadow-lg backdrop-blur-xl transition duration-300 ease-out hover:-translate-y-1.5 hover:border-brand/40 hover:shadow-2xl hover:shadow-brand/15"
                    data-reveal
                >
                    <div class="mb-2 flex items-center justify-between gap-2">
                        <span class="inline-flex items-center gap-1 rounded-full bg-brand/10 px-2.5 py-0.5 text-xs font-bold text-brand">{{ $task->topic }}</span>
                        @if ($bestScores->has($task->id))
                            <span class="inline-flex items-center gap-1 rounded-full bg-sun/15 px-2.5 py-0.5 text-xs font-bold text-sun">Band {{ number_format($bestScores[$task->id], 1) }}</span>
                        @endif
                    </div>
                    <h3 class="text-lg font-bold text-ink">{{ $task->title }}</h3>
                    <p class="mt-2 line-clamp-3 flex-1 text-sm text-ink/60">{{ $task->prompt }}</p>
                    <span class="mt-4 inline-flex items-center gap-1 text-sm font-bold text-brand">
                        {{ $bestScores->has($task->id) ? 'Попробовать снова' : 'Начать' }}
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" class="transition-transform group-hover:translate-x-1"><path d="M5 12h14M13 6l6 6-6 6" /></svg>
                    </span>
                </a>
            @endforeach
        </div>
    </div>
@endsection
