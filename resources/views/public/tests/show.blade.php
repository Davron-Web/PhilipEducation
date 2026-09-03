@extends('layouts.app')

@section('title', $test->title)
@section('page_title', 'Test')
@section('page_description', optional(optional($test->lesson)->level)->name ?? 'Self-check quiz')

@section('content')
    <div class="mx-auto max-w-3xl px-4 py-10 sm:px-6 lg:px-8">
        <a href="{{ route('tests.index') }}" class="mb-4 inline-flex items-center gap-1 text-sm font-semibold text-ink/60 transition hover:text-brand">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 12H5M11 18l-6-6 6-6" /></svg>
            Все тесты
        </a>

        <x-ui.card :hover="false" class="mb-6">
            <div class="mb-2 flex flex-wrap items-center gap-2">
                <x-ui.badge variant="level" :level="optional($test->lesson)->level" />
                <span class="inline-flex items-center gap-1 text-sm text-ink/50">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10" /><path d="M12 16v-4M12 8h.01" /></svg>
                    {{ $test->questions->count() }} вопросов
                </span>
                @if ($test->time_limit)
                    <span class="inline-flex items-center gap-1 text-sm text-ink/50">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10" /><polyline points="12 6 12 12 16 14" /></svg>
                        {{ $test->time_limit }} мин
                    </span>
                @endif
                <span class="inline-flex items-center gap-1 text-sm text-ink/50">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 11l3 3L22 4" /><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11" /></svg>
                    Проходной балл: {{ $test->passing_score }}%
                </span>
            </div>
            <h1 class="text-2xl font-extrabold text-ink">{{ $test->title }}</h1>
        </x-ui.card>

        @if ($test->questions->isEmpty())
            <x-ui.card :hover="false" class="py-16 text-center">
                <p class="text-ink/50">В этом тесте пока нет вопросов.</p>
            </x-ui.card>
        @else
            <form method="POST" action="{{ route('tests.submit', $test->id) }}" x-data="{ started: Date.now() }">
                @csrf
                {{-- Сколько времени занял тест — уходит в историю попыток. --}}
                <input type="hidden" name="duration_seconds" x-bind:value="Math.round((Date.now() - started) / 1000)">

                <x-ui.card :hover="false">
                    @foreach ($test->questions as $question)
                        <div class="mb-6 border-b border-line pb-6 last:border-0 last:pb-0">
                            <p class="mb-3 font-semibold text-ink">
                                {{ $loop->iteration }}. {{ $question->question }}
                                @if ($question->type === 'multiple_choice')
                                    <span class="ml-1 text-xs font-normal text-ink/40">(несколько вариантов)</span>
                                @endif
                            </p>

                            @if ($question->answers->isNotEmpty() && $question->type !== 'text')
                                <div class="space-y-2">
                                    @foreach ($question->answers as $answer)
                                        <label for="answer-{{ $answer->id }}" class="flex cursor-pointer items-center gap-2.5 rounded-xl border border-line bg-armor px-4 py-2.5 text-sm text-ink transition hover:border-brand/40">
                                            <input
                                                class="h-4 w-4 accent-brand"
                                                type="{{ $question->type === 'multiple_choice' ? 'checkbox' : 'radio' }}"
                                                name="answers[{{ $question->id }}]{{ $question->type === 'multiple_choice' ? '[]' : '' }}"
                                                id="answer-{{ $answer->id }}"
                                                value="{{ $answer->id }}"
                                            >
                                            {{ $answer->answer }}
                                        </label>
                                    @endforeach
                                </div>
                            @else
                                <x-ui.input type="text" :name="'answers['.$question->id.']'" placeholder="Ваш ответ…" />
                            @endif
                        </div>
                    @endforeach

                    <x-ui.button type="submit">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M4 15s1-1 4-1 5 2 8 2 4-1 4-1V3s-1 1-4 1-5-2-8-2-4 1-4 1z" /><path d="M4 22V15" /></svg>
                        Завершить тест
                    </x-ui.button>
                </x-ui.card>
            </form>
        @endif
    </div>
@endsection
