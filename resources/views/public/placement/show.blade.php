{{-- Тест на определение уровня: все 50 вопросов одной формой, без
     таймера — здесь важна точность ответа, а не скорость. --}}
@extends('layouts.app')

@section('title', 'Тест на определение уровня')
@section('page_title', 'Placement Test')

@section('content')
    <div class="mx-auto max-w-3xl px-4 py-10 sm:px-6 lg:px-8" x-data="{ answered: 0, total: {{ $questions->count() }} }">
        <div class="sticky top-0 z-10 -mx-4 mb-6 border-b border-line bg-armor/95 px-4 py-3 backdrop-blur sm:-mx-6 sm:px-6">
            <div class="mx-auto flex max-w-3xl items-center justify-between gap-4">
                <p class="text-sm font-semibold text-ink">Тест на определение уровня</p>
                <p class="text-sm text-ink/50">
                    Отвечено: <span class="font-bold text-brand" x-text="answered"></span> / {{ $questions->count() }}
                </p>
            </div>
            <div class="mx-auto mt-2 h-1.5 max-w-3xl overflow-hidden rounded-full bg-surface2">
                <div class="h-full rounded-full bg-brand transition-all" :style="`width: ${answered / total * 100}%`"></div>
            </div>
        </div>

        <form method="POST" action="{{ route('tests.placement.submit', $attempt->id) }}" @change="answered = $el.querySelectorAll('input[type=radio]:checked').length">
            @csrf

            <x-ui.card :hover="false">
                @foreach ($questions as $question)
                    <div class="mb-6 border-b border-line pb-6 last:border-0 last:pb-0">
                        <p class="mb-3 font-semibold text-ink">{{ $loop->iteration }}. {{ $question->question }}</p>
                        <div class="space-y-2">
                            @foreach ($question->options as $option)
                                <label for="option-{{ $option->id }}" class="flex min-h-12 cursor-pointer items-center gap-3 rounded-xl border border-line bg-armor px-4 py-3 text-sm text-ink transition hover:border-brand/40 has-[:checked]:border-brand has-[:checked]:bg-brand/5">
                                    <input
                                        class="h-5 w-5 shrink-0 accent-brand"
                                        type="radio"
                                        name="answers[{{ $question->id }}]"
                                        id="option-{{ $option->id }}"
                                        value="{{ $option->id }}"
                                    >
                                    {{ $option->text }}
                                </label>
                            @endforeach
                        </div>
                    </div>
                @endforeach

                <p class="mb-4 text-xs text-ink/50">
                    Не ответили на все вопросы? Ничего страшного — незаполненные
                    просто засчитаются как неверные, тест всё равно завершится.
                </p>

                <x-ui.button type="submit">
                    Завершить и узнать уровень
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M13 6l6 6-6 6" /></svg>
                </x-ui.button>
            </x-ui.card>
        </form>
    </div>
@endsection
