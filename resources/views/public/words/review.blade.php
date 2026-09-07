{{-- Сессия интервального повторения: карточка со словом, переворот на
     перевод, две кнопки — «вспомнил» / «забыл». Ответ уходит на сервер,
     который пересчитывает срок следующего показа. --}}
@extends('layouts.app')

@section('title', 'Повторение слов')
@section('page_title', 'Повторение')

@push('styles')
    <style>
        .rev-card { perspective: 1200px; }
        .rev-inner {
            position: relative;
            transform-style: preserve-3d;
            transition: transform .5s cubic-bezier(.22,1,.36,1);
        }
        .rev-card.is-flipped .rev-inner { transform: rotateY(180deg); }
        .rev-face { backface-visibility: hidden; -webkit-backface-visibility: hidden; }
        .rev-back { position: absolute; inset: 0; transform: rotateY(180deg); }
        @media (prefers-reduced-motion: reduce) { .rev-inner { transition: none; } }
    </style>
@endpush

@section('content')
    <div class="mx-auto max-w-2xl px-4 py-10 sm:px-6 lg:px-8" x-data="wordReview({{ Js::from($cards) }}, {{ $dueCount }})">
        <a href="{{ route('words.index') }}" class="mb-4 inline-flex items-center gap-1 text-sm font-semibold text-ink/60 transition hover:text-brand">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 12H5M11 18l-6-6 6-6" /></svg>
            {{ __('site.nav.vocabulary') }}
        </a>

        {{-- Нечего повторять --}}
        <template x-if="!cards.length">
            <x-ui.card :hover="false" class="py-16 text-center">
                <p class="font-display text-xl font-semibold text-ink">На сегодня всё повторено</p>
                <p class="mt-2 text-ink/60">Возвращайтесь, когда подойдёт срок следующих слов — или добавьте новые в словаре.</p>
                <x-ui.button :href="route('words.index')" class="mt-6">{{ __('site.nav.vocabulary') }}</x-ui.button>
            </x-ui.card>
        </template>

        <div x-show="cards.length" style="display:none" x-cloak>
            {{-- Прогресс сессии --}}
            <div class="mb-6 flex items-center justify-between text-sm font-semibold text-ink/50">
                <span x-text="'Карточка ' + (index + 1) + ' из ' + cards.length"></span>
                <span x-text="'Осталось повторить: ' + dueLeft"></span>
            </div>
            <div class="mb-8 h-1.5 overflow-hidden rounded-full bg-surface2">
                <div class="h-full rounded-full bg-brand transition-all duration-300" :style="'width: ' + Math.round(index / cards.length * 100) + '%'"></div>
            </div>

            {{-- Сессия закончена --}}
            <template x-if="finished">
                <x-ui.card :hover="false" class="py-14 text-center">
                    <p class="font-display text-2xl font-semibold text-ink">Сессия завершена</p>
                    <p class="mt-2 text-ink/60">
                        <span x-text="'Вспомнили: ' + stats.good + ' · забыли: ' + stats.bad"></span>
                    </p>
                    <div class="mt-6 flex flex-wrap justify-center gap-3">
                        <x-ui.button x-show="dueLeft > 0" href="{{ route('words.review') }}">Продолжить</x-ui.button>
                        <x-ui.button :href="route('words.index')" variant="outline">{{ __('site.nav.vocabulary') }}</x-ui.button>
                    </div>
                </x-ui.card>
            </template>

            {{-- Карточка --}}
            <template x-if="!finished && current">
                <div>
                    <div class="rev-card h-72 cursor-pointer" :class="{ 'is-flipped': flipped }" @click="flipped = !flipped">
                        <div class="rev-inner h-full w-full">
                            <div class="rev-face flex h-full flex-col items-center justify-center rounded-2xl border border-line bg-armor2 p-8 text-center shadow-softLg">
                                <p class="text-xs font-bold uppercase tracking-widest text-ink/40">{{ __('site.home.flip_word') }}</p>
                                <p class="mt-3 flex items-center gap-2 font-display text-3xl font-semibold text-ink">
                                    <span x-text="current.word"></span>
                                    <button
                                        type="button"
                                        @click.stop="pronounce(current.word, null)"
                                        class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-brand/10 text-brand transition hover:bg-brand hover:text-white"
                                        aria-label="Прослушать произношение"
                                    >
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="15" height="15"><polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5" /><path d="M15.54 8.46a5 5 0 0 1 0 7.07" /></svg>
                                    </button>
                                </p>
                                <p class="mt-1 font-mono text-sm text-sky" x-show="current.transcription" x-text="'/' + current.transcription + '/'"></p>
                                <p class="mt-6 text-xs text-ink/40">{{ __('site.home.flip_hint') }}</p>
                            </div>

                            <div class="rev-face rev-back flex h-full flex-col items-center justify-center rounded-2xl bg-navy p-8 text-center shadow-softLg">
                                <p class="text-xs font-bold uppercase tracking-widest text-gold">{{ __('site.home.flip_translation') }}</p>
                                <p class="mt-3 font-display text-2xl font-semibold text-white" x-text="current.translation"></p>
                                <p class="mt-4 text-sm italic text-white/50" x-show="current.example" x-text="'«' + current.example + '»'"></p>
                            </div>
                        </div>
                    </div>

                    {{-- Оценка доступна только после переворота: иначе можно
                         нажать «вспомнил», не проверив себя. --}}
                    <div class="mt-6 flex justify-center gap-3" x-show="flipped" style="display:none">
                        <x-ui.button variant="outline" @click="answer(false)" x-bind:disabled="sending">Забыл</x-ui.button>
                        <x-ui.button @click="answer(true)" x-bind:disabled="sending">Вспомнил</x-ui.button>
                    </div>
                    <p class="mt-4 text-center text-xs text-ink/40" x-show="!flipped">Переверните карточку, чтобы оценить себя</p>
                </div>
            </template>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function wordReview(cards, dueCount) {
            return {
                cards: cards,
                index: 0,
                flipped: false,
                sending: false,
                finished: false,
                dueLeft: dueCount,
                stats: { good: 0, bad: 0 },

                get current() {
                    return this.cards[this.index] || null;
                },

                answer: function (remembered) {
                    if (this.sending || !this.current) return;
                    this.sending = true;

                    var self = this;
                    var card = this.current;

                    fetch('/words/' + card.word_id + '/review', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        },
                        body: JSON.stringify({ remembered: remembered }),
                    })
                        .then(function (r) { return r.json(); })
                        .then(function (data) {
                            if (typeof data.due_left === 'number') self.dueLeft = data.due_left;
                        })
                        .catch(function () {})
                        .finally(function () {
                            remembered ? self.stats.good++ : self.stats.bad++;
                            self.flipped = false;
                            self.sending = false;

                            if (self.index + 1 < self.cards.length) {
                                self.index++;
                            } else {
                                self.finished = true;
                            }
                        });
                },
            };
        }
    </script>
@endpush
