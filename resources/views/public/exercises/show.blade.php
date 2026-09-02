@extends('layouts.app')

@section('title', $exercise->title)
@section('page_title', 'Exercise')
@section('page_description', $exercise->type_label)

@section('content')
    @php
        $badgeClasses = match ($exercise->type_badge_color) {
            'info' => 'border border-sky/30 bg-sky/10 text-sky',
            'warning' => 'border border-sun/30 bg-sun/10 text-sun',
            'primary' => 'border border-brand/40 bg-brand/10 text-brand',
            'success' => 'border border-green-500/30 bg-green-500/10 text-green-600 dark:text-green-400',
            default => 'border border-line bg-surface2 text-ink/50',
        };
    @endphp

    <div class="mx-auto max-w-2xl px-4 py-10 sm:px-6 lg:px-8">
        <a href="{{ route('exercises.index') }}" class="mb-4 inline-flex items-center gap-1 text-sm font-semibold text-ink/60 transition hover:text-brand">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 12H5M11 18l-6-6 6-6" /></svg>
            Все упражнения
        </a>

        <x-ui.card :hover="false" class="mb-6">
            <span class="inline-flex items-center gap-1 rounded-full px-3 py-1 text-xs font-bold {{ $badgeClasses }}">{{ $exercise->type_label }}</span>
            <h1 class="mt-2 text-2xl font-extrabold text-ink">{{ $exercise->title }}</h1>
            <p class="mt-1 text-ink/60">{{ $exercise->instructions }}</p>
        </x-ui.card>

        @if ($exercise->questions->isEmpty())
            <x-ui.card :hover="false" class="py-16 text-center">
                <p class="text-ink/50">В этом упражнении пока нет вопросов.</p>
            </x-ui.card>
        @else
            <form data-exercise-check>
                <x-ui.card :hover="false">
                    @foreach ($exercise->questions as $question)
                        <div class="mb-5">
                            <label class="mb-1.5 block font-semibold text-ink">{{ $loop->iteration }}. {{ $question->question }}</label>
                            <x-ui.input
                                type="text"
                                :name="'answer-'.$question->id"
                                placeholder="Введите ответ…"
                                data-answer="{{ $question->correct_answer }}"
                            />
                        </div>
                    @endforeach

                    <x-ui.button type="submit">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 6 9 17l-5-5" /></svg>
                        Проверить ответы
                    </x-ui.button>
                </x-ui.card>
            </form>
        @endif
    </div>
@endsection
