{{-- Словарь: список слов с фильтром и режим карточек (переворот 3D,
     «Знаю» / «Повторить»). Фото слов сознательно не показываем. --}}
@extends('layouts.app')

@section('title', 'Словарь')
@section('page_title', 'Словарь')
@section('meta_description', 'Учите английские слова: список и карточки с переворотом, для Philip Education.')

@push('styles')
    <style>
        .flip-card { perspective: 1400px; }
        .flip-card-inner {
            position: relative;
            transform-style: preserve-3d;
            transition: transform .5s cubic-bezier(.22, 1, .36, 1);
        }
        .flip-card.is-flipped .flip-card-inner { transform: rotateY(180deg); }
        .flip-card-face {
            backface-visibility: hidden;
            -webkit-backface-visibility: hidden;
        }
        .flip-card-back { position: absolute; inset: 0; transform: rotateY(180deg); }
        @media (prefers-reduced-motion: reduce) {
            .flip-card-inner { transition: none; }
        }
    </style>
@endpush

@push('scripts')
    <script>
        function wordDeck(cards) {
            return {
                cards: cards,
                index: 0,
                flipped: false,
                get filteredCards() {
                    var list = this.cards;
                    if (this.filter === 'learned') list = list.filter(c => c.learned);
                    if (this.filter === 'new') list = list.filter(c => !c.learned);
                    if (this.category !== 'all') list = list.filter(c => c.category === this.category);
                    return list;
                },
                get current() {
                    var list = this.filteredCards;
                    if (!list.length) return null;
                    if (this.index >= list.length) this.index = 0;
                    return list[this.index];
                },
                flip() { this.flipped = !this.flipped; },
                next() {
                    this.flipped = false;
                    var list = this.filteredCards;
                    this.index = list.length ? (this.index + 1) % list.length : 0;
                },
                async mark(learned) {
                    var card = this.current;
                    if (!card) return;
                    card.learned = learned;
                    try {
                        await fetch('/words/' + card.id + '/progress', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            },
                            body: JSON.stringify({ learned: learned }),
                        });
                    } catch (e) {}
                    this.next();
                },
            };
        }
    </script>
@endpush

@section('content')
    @php
        $flashcards = $words->map(fn ($w) => [
            'id' => $w->id,
            'word' => $w->word,
            'transcription' => $w->transcription,
            'translation' => $w->translations->pluck('translation')->join(', ') ?: '—',
            'learned' => in_array($w->id, $learnedWordIds, true),
            'category' => $w->category,
        ])->values();
    @endphp

    <div
        class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8"
        x-data="Object.assign(wordDeck({{ Js::from($flashcards) }}), { mode: 'list', filter: 'all', category: 'all', addWordOpen: false })"
    >
        <div class="mb-8 flex flex-wrap items-end justify-between gap-4" data-reveal>
            <div>
                <h1 class="text-3xl font-extrabold text-ink sm:text-4xl">Словарь</h1>
                <p class="mt-1 text-ink/60">{{ $words->count() }} слов — {{ count($learnedWordIds) }} уже выучено.</p>
            </div>

            <div class="flex items-center gap-2">
                <x-ui.button variant="outline" size="sm" @click="addWordOpen = true">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 5v14M5 12h14" /></svg>
                    Добавить своё слово
                </x-ui.button>

                <div class="flex rounded-xl border border-ink/10 bg-white/70 p-1">
                    <button type="button" @click="mode = 'list'" :class="mode === 'list' ? 'bg-brand text-white shadow' : 'text-ink/60'" class="rounded-lg px-3 py-1.5 text-sm font-bold transition">Список</button>
                    <button type="button" @click="mode = 'cards'; index = 0; flipped = false" :class="mode === 'cards' ? 'bg-brand text-white shadow' : 'text-ink/60'" class="rounded-lg px-3 py-1.5 text-sm font-bold transition">Карточки</button>
                </div>
            </div>
        </div>

        {{-- Фильтр по статусу --}}
        <div class="mb-4 flex flex-wrap gap-2" data-reveal>
            <button type="button" @click="filter = 'all'" :class="filter === 'all' ? 'bg-brand text-white shadow-md shadow-brand/25' : 'bg-white/70 text-ink/60 hover:bg-brand/5'" class="rounded-full px-4 py-1.5 text-sm font-bold transition">Все слова</button>
            <button type="button" @click="filter = 'learned'" :class="filter === 'learned' ? 'bg-brand text-white shadow-md shadow-brand/25' : 'bg-white/70 text-ink/60 hover:bg-brand/5'" class="rounded-full px-4 py-1.5 text-sm font-bold transition">Выучено</button>
            <button type="button" @click="filter = 'new'" :class="filter === 'new' ? 'bg-brand text-white shadow-md shadow-brand/25' : 'bg-white/70 text-ink/60 hover:bg-brand/5'" class="rounded-full px-4 py-1.5 text-sm font-bold transition">На изучении</button>
        </div>

        {{-- Фильтр по категории --}}
        @if ($categories->isNotEmpty())
            <div class="mb-8 flex flex-wrap gap-1.5" data-reveal>
                <button type="button" @click="category = 'all'" :class="category === 'all' ? 'bg-sun text-ink shadow-md shadow-sun/30' : 'bg-white/70 text-ink/50 hover:bg-sun/10'" class="rounded-full px-3 py-1 text-xs font-bold transition">Все темы</button>
                @foreach ($categories as $cat)
                    <button
                        type="button"
                        @click="category = {{ Js::from($cat) }}"
                        :class="category === {{ Js::from($cat) }} ? 'bg-sun text-ink shadow-md shadow-sun/30' : 'bg-white/70 text-ink/50 hover:bg-sun/10'"
                        class="rounded-full px-3 py-1 text-xs font-bold transition"
                    >{{ $cat }}</button>
                @endforeach
            </div>
        @endif

        @if ($words->isEmpty())
            <x-ui.card :hover="false" class="py-16 text-center">
                <p class="text-lg font-semibold text-ink">Слов пока нет</p>
                <p class="mt-1 text-ink/50">Добавьте своё первое слово кнопкой выше.</p>
            </x-ui.card>
        @else
            {{-- ===== Список ===== --}}
            <div x-show="mode === 'list'" class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($words as $word)
                    @php $isLearned = in_array($word->id, $learnedWordIds, true); @endphp
                    <div
                        x-show="(filter === 'all' || (filter === 'learned') === {{ $isLearned ? 'true' : 'false' }}) && (category === 'all' || category === {{ Js::from($word->category) }})"
                    >
                        <a
                            href="{{ route('words.show', $word->id) }}"
                            class="group flex h-full flex-col rounded-2xl border border-white/60 bg-white/60 p-5 shadow-lg shadow-ink/5 backdrop-blur-xl transition duration-300 ease-out hover:-translate-y-1.5 hover:border-brand/30 hover:shadow-2xl hover:shadow-brand/15"
                            data-reveal
                        >
                            <div class="mb-2 flex items-start justify-between gap-2">
                                <div>
                                    <h3 class="flex items-center gap-1.5 text-lg font-bold capitalize text-ink">
                                        {{ $word->word }}
                                        <x-speak-button :word="$word->word" :audio-url="$word->audio_url" />
                                    </h3>
                                    @if ($word->transcription)
                                        <span class="text-sm text-ink/40">/{{ $word->transcription }}/</span>
                                    @endif
                                </div>
                                <x-ui.badge :variant="$isLearned ? 'success' : 'neutral'">{{ $isLearned ? 'Выучено' : 'Новое' }}</x-ui.badge>
                            </div>

                            @if ($word->translations->isNotEmpty())
                                <p class="font-semibold text-brand">{{ $word->translations->pluck('translation')->join(', ') }}</p>
                            @endif

                            @if ($word->example)
                                <p class="mt-2 flex-1 text-sm italic text-ink/50">&laquo;{{ $word->example }}&raquo;</p>
                            @endif

                            @if ($word->category)
                                <span class="mt-3 inline-flex w-fit items-center rounded-full bg-sun/15 px-2.5 py-0.5 text-[11px] font-bold text-amber-700">{{ $word->category }}</span>
                            @endif
                        </a>
                    </div>
                @endforeach
            </div>

            {{-- ===== Карточки (флеш-карты) ===== --}}
            <div x-show="mode === 'cards'" style="display:none" x-cloak>
                <template x-if="!current">
                    <p class="py-16 text-center text-ink/50">В этом фильтре пока нет слов.</p>
                </template>

                <template x-if="current">
                    <div class="mx-auto max-w-md" data-reveal>
                        <div
                            class="flip-card h-72 cursor-pointer"
                            :class="{ 'is-flipped': flipped }"
                            @click="flip()"
                        >
                            <div class="flip-card-inner h-full w-full">
                                <div class="flip-card-face flex h-full flex-col items-center justify-center rounded-3xl border border-white/60 bg-gradient-to-br from-white to-sky-50 p-8 text-center shadow-2xl shadow-brand/10">
                                    <p class="text-xs font-bold uppercase tracking-widest text-ink/40">Слово</p>
                                    <p class="mt-3 flex items-center gap-2 text-3xl font-extrabold capitalize text-ink">
                                        <span x-text="current.word"></span>
                                        <button
                                            type="button"
                                            @click.stop="pronounce(current.word, null)"
                                            class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-brand/10 text-brand transition hover:bg-brand hover:text-white active:scale-90"
                                            aria-label="Прослушать произношение"
                                        >
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="15" height="15"><polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5" /><path d="M15.54 8.46a5 5 0 0 1 0 7.07" /><path d="M19.07 4.93a10 10 0 0 1 0 14.14" /></svg>
                                        </button>
                                    </p>
                                    <p class="mt-2 text-ink/40" x-show="current.transcription" x-text="'/' + current.transcription + '/'"></p>
                                    <p class="mt-6 text-xs text-ink/30">Нажмите, чтобы перевернуть</p>
                                </div>
                                <div class="flip-card-face flip-card-back flex h-full flex-col items-center justify-center rounded-3xl bg-gradient-to-br from-ink via-brand to-sky p-8 text-center text-white shadow-2xl">
                                    <p class="text-xs font-bold uppercase tracking-widest text-white/60">Перевод</p>
                                    <p class="mt-3 text-3xl font-extrabold" x-text="current.translation"></p>
                                </div>
                            </div>
                        </div>

                        <div class="mt-6 flex justify-center gap-3">
                            <x-ui.button variant="outline" @click.stop="mark(false)">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M1 4v6h6M23 20v-6h-6" /><path d="M20.49 9A9 9 0 0 0 5.64 5.64L1 10m22 4-4.64 4.36A9 9 0 0 1 3.51 15" /></svg>
                                Повторить
                            </x-ui.button>
                            <x-ui.button variant="primary" @click.stop="mark(true)">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 6 9 17l-5-5" /></svg>
                                Знаю
                            </x-ui.button>
                        </div>

                        <p class="mt-4 text-center text-sm font-semibold text-ink/40" x-text="(index + 1) + ' / ' + filteredCards.length"></p>
                    </div>
                </template>
            </div>
        @endif

        {{-- ===== Модалка «Добавить своё слово» ===== --}}
        <div
            x-show="addWordOpen"
            style="display:none"
            x-cloak
            class="fixed inset-0 z-[60] flex items-center justify-center bg-ink/40 p-4 backdrop-blur-sm"
            @keydown.escape.window="addWordOpen = false"
        >
            <div @click.outside="addWordOpen = false" class="w-full max-w-md rounded-3xl border border-white/60 bg-white p-6 shadow-2xl">
                <div class="mb-4 flex items-center justify-between">
                    <h2 class="text-lg font-bold text-ink">Добавить своё слово</h2>
                    <button type="button" @click="addWordOpen = false" class="text-ink/40 hover:text-ink"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6 6 18M6 6l12 12" /></svg></button>
                </div>
                <form method="POST" action="{{ route('words.store') }}" class="space-y-4">
                    @csrf
                    <x-ui.input name="word" label="Слово (на английском)" placeholder="например, resilient" required />
                    <x-ui.input name="translation" label="Перевод" placeholder="например, стойкий" required />
                    <x-ui.input name="example" label="Пример предложения (необязательно)" placeholder="He stayed resilient through hard times." />
                    <x-ui.button type="submit" variant="primary" class="w-full">Добавить слово</x-ui.button>
                </form>
            </div>
        </div>
    </div>
@endsection
