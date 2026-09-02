{{-- IELTS Speaking Part 2: cue card + таймер (подготовка -> ответ).
     Только таймер, без записи/распознавания речи — инструмент для
     самостоятельной практики вслух. --}}
@extends('layouts.app')

@section('title', $card->title)
@section('page_title', 'IELTS Speaking')
@section('meta_description', Str::limit($card->prompt, 150))

@section('content')
    <div
        class="mx-auto max-w-2xl px-4 py-10 sm:px-6 lg:px-8"
        x-data="{
            phase: 'idle',
            remaining: 0,
            prepSeconds: {{ $card->prep_seconds }},
            speakSeconds: {{ $card->speak_seconds }},
            timer: null,
            start() {
                this.phase = 'prep';
                this.remaining = this.prepSeconds;
                this.tick();
            },
            tick() {
                clearInterval(this.timer);
                this.timer = setInterval(() => {
                    this.remaining--;
                    if (this.remaining <= 0) {
                        if (this.phase === 'prep') {
                            this.phase = 'speak';
                            this.remaining = this.speakSeconds;
                        } else {
                            clearInterval(this.timer);
                            this.phase = 'done';
                        }
                    }
                }, 1000);
            },
            reset() {
                clearInterval(this.timer);
                this.phase = 'idle';
                this.remaining = 0;
            },
            label() {
                var m = Math.floor(this.remaining / 60);
                var s = this.remaining % 60;
                return m + ':' + (s < 10 ? '0' : '') + s;
            },
        }"
    >
        <a href="{{ route('ielts.speaking.index') }}" class="mb-6 inline-flex items-center gap-1 text-sm font-semibold text-ink/60 transition hover:text-sun">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 12H5M11 18l-6-6 6-6" /></svg>
            Все карточки
        </a>

        <x-ui.card :hover="false">
            @if ($card->topic)
                <span class="mb-2 inline-flex w-fit items-center gap-1 rounded-full bg-sun/10 px-2.5 py-0.5 text-xs font-bold text-sun">{{ $card->topic }}</span>
            @endif
            <h1 class="text-2xl font-extrabold text-ink sm:text-3xl">{{ $card->title }}</h1>
            <p class="mt-3 text-ink/80">{{ $card->prompt }}</p>

            @if (!empty($card->cue_points))
                <ul class="mt-4 space-y-1.5">
                    @foreach ($card->cue_points as $point)
                        <li class="flex items-start gap-2 text-sm text-ink/70">
                            <span class="mt-1.5 h-1.5 w-1.5 shrink-0 rounded-full bg-sun"></span>
                            {{ $point }}
                        </li>
                    @endforeach
                </ul>
            @endif
        </x-ui.card>

        <x-ui.card :hover="false" class="mt-6 text-center">
            <template x-if="phase === 'idle'">
                <div>
                    <p class="mb-4 text-sm text-ink/60">{{ $card->prep_seconds }} сек на подготовку, затем {{ $card->speak_seconds }} сек на ответ вслух.</p>
                    <x-ui.button variant="accent" size="lg" @click="start()">Начать таймер</x-ui.button>
                </div>
            </template>

            <template x-if="phase === 'prep' || phase === 'speak'">
                <div>
                    <p
                        class="text-xs font-bold uppercase tracking-widest"
                        :class="phase === 'prep' ? 'text-skylight' : 'text-sun'"
                        x-text="phase === 'prep' ? 'Подготовка' : 'Говорите'"
                    ></p>
                    <p class="mt-2 font-display text-6xl font-extrabold text-ink" x-text="label()"></p>
                    <x-ui.button variant="outline" class="mt-6" @click="reset()">Сбросить</x-ui.button>
                </div>
            </template>

            <template x-if="phase === 'done'">
                <div>
                    <p class="text-2xl font-extrabold text-ink">Время вышло</p>
                    <p class="mt-1 text-sm text-ink/60">Отличная практика! Можно попробовать ещё раз.</p>
                    <x-ui.button variant="accent" class="mt-6" @click="start()">Повторить</x-ui.button>
                </div>
            </template>
        </x-ui.card>
    </div>
@endsection
