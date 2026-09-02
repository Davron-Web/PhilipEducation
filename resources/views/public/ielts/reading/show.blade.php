{{-- IELTS Reading: текст + вопросы. После отправки показываем разбор
     последней попытки (что было выбрано и что правильно). --}}
@extends('layouts.app')

@section('title', $passage->title)
@section('page_title', 'IELTS Reading')
@section('meta_description', Str::limit(strip_tags($passage->passage_text), 150))

@section('content')
    <div class="mx-auto max-w-3xl px-4 py-10 sm:px-6 lg:px-8">
        <a href="{{ route('ielts.reading.index') }}" class="mb-6 inline-flex items-center gap-1 text-sm font-semibold text-ink/60 transition hover:text-skylight">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 12H5M11 18l-6-6 6-6" /></svg>
            Все тексты
        </a>

        <x-ui.card :hover="false">
            <x-ui.badge variant="level" :level="$passage->level" class="mb-3" />
            <h1 class="text-2xl font-extrabold text-ink sm:text-3xl">{{ $passage->title }}</h1>
            <div class="mt-4 whitespace-pre-line text-sm leading-relaxed text-ink/80">{{ $passage->passage_text }}</div>
        </x-ui.card>

        <x-ui.card :hover="false" class="mt-6">
            <h2 class="mb-4 text-lg font-bold text-ink">Вопросы</h2>

            <form method="POST" action="{{ route('ielts.reading.submit', $passage) }}" class="space-y-6">
                @csrf
                @foreach ($passage->questions as $index => $question)
                    @php $chosen = $lastAttempt->answers[$index] ?? null; @endphp
                    <div>
                        <p class="mb-2 font-semibold text-ink">{{ $index + 1 }}. {{ $question->question }}</p>
                        <div class="space-y-1.5">
                            @foreach ($question->options as $optIndex => $option)
                                @php
                                    $isCorrect = $optIndex === $question->correct_index;
                                    $isChosenWrong = $lastAttempt && $chosen == $optIndex && ! $isCorrect;
                                @endphp
                                <label
                                    class="flex cursor-pointer items-center gap-2 rounded-xl border px-3 py-2 text-sm transition
                                        {{ $lastAttempt && $isCorrect ? 'border-green-400/50 bg-green-400/10 text-green-300' : '' }}
                                        {{ $isChosenWrong ? 'border-red-400/50 bg-red-400/10 text-red-300' : '' }}
                                        {{ ! $lastAttempt ? 'border-line hover:border-sky/40 hover:bg-surface2' : '' }}"
                                >
                                    <input type="radio" name="answers[{{ $index }}]" value="{{ $optIndex }}" class="accent-sky" required @checked($chosen == $optIndex)>
                                    {{ $option }}
                                </label>
                            @endforeach
                        </div>
                    </div>
                @endforeach

                <x-ui.button type="submit" variant="primary">Проверить ответы</x-ui.button>
            </form>
        </x-ui.card>
    </div>
@endsection
