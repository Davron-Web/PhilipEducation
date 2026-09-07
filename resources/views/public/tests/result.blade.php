{{-- Результат попытки: балл, вердикт и разбор каждого вопроса. --}}
@extends('layouts.app')

@section('title', $attempt->test->title)
@section('page_title', 'Результат теста')

@section('content')
    @php
        $test = $attempt->test;
        // Ответы попытки по id вопроса — чтобы не искать в цикле.
        $given = $attempt->answers->keyBy('question_id');
        $correctCount = $attempt->answers->where('is_correct', true)->count();
    @endphp

    <div class="mx-auto max-w-3xl px-4 py-10 sm:px-6 lg:px-8">
        <a href="{{ route('tests.index') }}" class="mb-4 inline-flex items-center gap-1 text-sm font-semibold text-ink/60 transition hover:text-brand">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 12H5M11 18l-6-6 6-6" /></svg>
            {{ __('site.nav.tests') }}
        </a>

        {{-- Итог --}}
        <div class="mb-6 overflow-hidden rounded-2xl border {{ $attempt->passed ? 'border-green-500/40' : 'border-sun/40' }} bg-armor2 shadow-soft" data-reveal>
            <div class="{{ $attempt->passed ? 'bg-green-500/10' : 'bg-sun/10' }} px-6 py-8 text-center">
                <p class="font-display text-5xl font-semibold {{ $attempt->passed ? 'text-green-600 dark:text-green-400' : 'text-sun' }}">
                    {{ $attempt->score }}%
                </p>
                <p class="mt-2 font-bold text-ink">
                    {{ $attempt->passed ? 'Тест пройден' : 'Тест не пройден' }}
                </p>
                <p class="mt-1 text-sm text-ink/50">
                    Правильных ответов: {{ $correctCount }} из {{ $attempt->answers->count() }}
                    · проходной балл {{ $test->passing_score }}%
                    @if ($attempt->duration_seconds)
                        · время {{ gmdate($attempt->duration_seconds >= 3600 ? 'H:i:s' : 'i:s', $attempt->duration_seconds) }}
                    @endif
                </p>
            </div>

            <div class="flex flex-wrap justify-center gap-3 border-t border-line px-6 py-4">
                <x-ui.button :href="route('tests.show', $test->id)">Пройти ещё раз</x-ui.button>
                <x-ui.button :href="route('tests.index')" variant="outline">{{ __('site.nav.tests') }}</x-ui.button>
            </div>
        </div>

        {{-- Разбор --}}
        <h2 class="mb-3 font-display text-lg font-semibold text-ink">Разбор ответов</h2>

        <div class="space-y-4">
            @foreach ($test->questions as $question)
                @php
                    $answer = $given->get($question->id);
                    $isCorrect = $answer?->is_correct ?? false;
                    $chosenIds = collect(explode(',', (string) ($answer->answer ?? '')))
                        ->filter(fn ($v) => is_numeric($v))
                        ->map(fn ($v) => (int) $v);
                @endphp

                <x-ui.card :hover="false" class="border-l-4 {{ $isCorrect ? '!border-l-green-500' : '!border-l-red-400' }}">
                    <div class="flex items-start justify-between gap-3">
                        <p class="font-semibold text-ink">{{ $loop->iteration }}. {{ $question->question }}</p>
                        <x-ui.badge :variant="$isCorrect ? 'success' : 'danger'">
                            {{ $isCorrect ? 'Верно' : 'Ошибка' }}
                        </x-ui.badge>
                    </div>

                    @if ($question->type === 'text')
                        <p class="mt-3 text-sm text-ink/60">
                            Ваш ответ: <span class="font-semibold text-ink">{{ $answer?->answer ?: '—' }}</span>
                        </p>
                        @unless ($isCorrect)
                            <p class="mt-1 text-sm text-ink/60">
                                Правильный ответ:
                                <span class="font-semibold text-green-600 dark:text-green-400">
                                    {{ $question->answers->where('is_correct', true)->pluck('answer')->join(' / ') ?: '—' }}
                                </span>
                            </p>
                        @endunless
                    @else
                        <div class="mt-3 space-y-1.5">
                            @foreach ($question->answers as $option)
                                @php
                                    $wasChosen = $chosenIds->contains($option->id);
                                    $isRight = (bool) $option->is_correct;
                                @endphp
                                <div @class([
                                    'flex items-center gap-2 rounded-lg border px-3 py-2 text-sm',
                                    'border-green-500/40 bg-green-500/5 text-green-700 dark:text-green-400' => $isRight,
                                    'border-red-400/40 bg-red-500/5 text-red-600 dark:text-red-400' => $wasChosen && ! $isRight,
                                    'border-line text-ink/50' => ! $isRight && ! $wasChosen,
                                ])>
                                    @if ($isRight)
                                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" class="shrink-0"><path d="M20 6 9 17l-5-5" /></svg>
                                    @elseif ($wasChosen)
                                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" class="shrink-0"><path d="M18 6 6 18M6 6l12 12" /></svg>
                                    @else
                                        <span class="h-[15px] w-[15px] shrink-0"></span>
                                    @endif

                                    <span>{{ $option->answer }}</span>

                                    @if ($wasChosen)
                                        <span class="ml-auto text-xs font-bold uppercase tracking-wider opacity-70">ваш выбор</span>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endif

                    {{-- Объяснение показываем только там, где ученик ошибся:
                         после верного ответа оно лишь удлиняет разбор. --}}
                    @if (! $isCorrect && $question->explanation)
                        <div class="mt-3 flex gap-2.5 rounded-lg border border-brand/25 bg-brand/5 p-3">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mt-0.5 shrink-0 text-brand"><path d="M9 18h6M10 22h4M12 2a7 7 0 0 0-4 12.7V17h8v-2.3A7 7 0 0 0 12 2Z" /></svg>
                            <p class="text-sm leading-relaxed text-ink/75">{{ $question->explanation }}</p>
                        </div>
                    @endif
                </x-ui.card>
            @endforeach
        </div>
    </div>
@endsection
