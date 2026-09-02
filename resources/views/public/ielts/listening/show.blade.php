{{-- IELTS Listening: озвучка через встроенный speechSynthesis (см. pronounce.js),
     транскрипт скрыт до ответа — как на настоящем экзамене. --}}
@extends('layouts.app')

@section('title', $passage->title)
@section('page_title', 'IELTS Listening')
@section('meta_description', 'Аудирование IELTS: слушайте запись и отвечайте на вопросы.')

@section('content')
    <div class="mx-auto max-w-3xl px-4 py-10 sm:px-6 lg:px-8" x-data="{
        playing: false,
        played: false,
        showTranscript: {{ $lastAttempt ? 'true' : 'false' }},
        play() {
            if (!('speechSynthesis' in window)) { this.showTranscript = true; return; }
            this.playing = true;
            this.played = true;
            speak({{ Js::from($passage->passage_text) }});
            var check = setInterval(() => {
                if (!window.speechSynthesis.speaking) { this.playing = false; clearInterval(check); }
            }, 300);
        },
    }">
        <a href="{{ route('ielts.listening.index') }}" class="mb-6 inline-flex items-center gap-1 text-sm font-semibold text-ink/60 transition hover:text-sky">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 12H5M11 18l-6-6 6-6" /></svg>
            Все записи
        </a>

        <x-ui.card :hover="false">
            <x-ui.badge variant="level" :level="$passage->level" class="mb-3" />
            <h1 class="text-2xl font-extrabold text-ink sm:text-3xl">{{ $passage->title }}</h1>

            <div class="mt-5 flex items-center gap-4">
                <button
                    type="button"
                    @click="play()"
                    class="flex h-16 w-16 shrink-0 items-center justify-center rounded-full bg-gradient-to-br from-sky to-skylight text-armor shadow-lg shadow-sky/30 transition hover:-translate-y-0.5 hover:shadow-xl"
                >
                    <svg x-show="!playing" width="26" height="26" viewBox="0 0 24 24" fill="currentColor"><path d="M8 5v14l11-7z" /></svg>
                    <svg x-show="playing" width="24" height="24" viewBox="0 0 24 24" fill="currentColor" style="display:none"><rect x="6" y="5" width="4" height="14" /><rect x="14" y="5" width="4" height="14" /></svg>
                </button>
                <div>
                    <p class="font-semibold text-ink" x-text="playing ? 'Идёт воспроизведение…' : (played ? 'Можно прослушать ещё раз' : 'Нажмите, чтобы начать')"></p>
                    <button type="button" @click="showTranscript = !showTranscript" class="mt-1 text-sm font-bold text-sky hover:underline" x-text="showTranscript ? 'Скрыть транскрипт' : 'Показать транскрипт'"></button>
                </div>
            </div>

            <div x-show="showTranscript" x-transition class="mt-5 whitespace-pre-line rounded-xl bg-white/5 p-4 text-sm leading-relaxed text-ink/80" style="display:none">{{ $passage->passage_text }}</div>
        </x-ui.card>

        <x-ui.card :hover="false" class="mt-6">
            <h2 class="mb-4 text-lg font-bold text-ink">Вопросы</h2>

            <form method="POST" action="{{ route('ielts.listening.submit', $passage) }}" class="space-y-6">
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
