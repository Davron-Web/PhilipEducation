{{-- Страница коллекции слов (по уровню/теме/экзамену) — список, флеш-карточки
     и мини-тест, тем же паттерном, что и у категорий словаря/выражений. --}}
@extends('layouts.app')

@section('title', $collection->title)
@section('page_title', 'Словарь')
@section('meta_description', "Коллекция «{$collection->title}» — слова с переводом, флеш-карточками и тестом, Philip Education.")

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

@section('content')
    @php
        $flashcards = $words->map(fn ($w) => [
            'id' => $w->id,
            'word' => $w->word,
            'transcription' => $w->transcription,
            'translation' => $w->translations->pluck('translation')->join(', ') ?: '—',
            'learned' => in_array($w->id, $learnedWordIds, true),
        ])->values();
        $learnedInCollection = $words->filter(fn ($w) => in_array($w->id, $learnedWordIds, true))->count();
    @endphp

    <div
        class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8"
        x-data="mergeAlpineData(flashcardDeck({{ Js::from($flashcards) }}, '/words'), wordQuiz({{ Js::from($flashcards) }}), { mode: 'list', filter: 'all' })"
    >
        <a href="{{ route('words.index') }}" class="mb-4 inline-flex items-center gap-1 text-sm font-semibold text-ink/60 transition hover:text-brand" data-reveal>
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 12H5M11 18l-6-6 6-6" /></svg>
            {{ __('site.vocabulary_collections.back_to_hub') }}
        </a>

        <div class="mb-6 flex flex-wrap items-end justify-between gap-4" data-reveal>
            <div>
                <h1 class="text-3xl font-extrabold text-ink sm:text-4xl">{{ $collection->title }}</h1>
                @if ($collection->description)
                    <p class="mt-1 text-ink/60">{{ $collection->description }}</p>
                @endif
                <p class="mt-1 text-sm text-ink/50">{{ __('site.vocabulary_collections.learned_of_total', ['learned' => $learnedInCollection, 'total' => $words->count()]) }}</p>
            </div>

            <div class="flex rounded-xl border border-line bg-armor2/70 p-1">
                <button type="button" @click="mode = 'list'" :class="mode === 'list' ? 'bg-brand text-white shadow' : 'text-ink/60'" class="rounded-lg px-3 py-1.5 text-sm font-bold transition">Список</button>
                <button type="button" @click="mode = 'cards'; index = 0; flipped = false" :class="mode === 'cards' ? 'bg-brand text-white shadow' : 'text-ink/60'" class="rounded-lg px-3 py-1.5 text-sm font-bold transition">{{ __('site.vocabulary_collections.flashcards_button') }}</button>
                <button type="button" @click="mode = 'quiz'; startQuiz()" :class="mode === 'quiz' ? 'bg-brand text-white shadow' : 'text-ink/60'" class="rounded-lg px-3 py-1.5 text-sm font-bold transition">{{ __('site.vocabulary_collections.test_button') }}</button>
            </div>
        </div>

        @if ($words->isEmpty())
            <x-ui.card :hover="false" class="py-16 text-center">
                <p class="text-lg font-semibold text-ink">{{ __('site.vocabulary_collections.no_words_yet') }}</p>
            </x-ui.card>
        @else
            {{-- ===== Фильтр по статусу (только для списка/карточек) ===== --}}
            <div class="mb-8 flex flex-wrap gap-2" x-show="mode !== 'quiz'" data-reveal>
                <button type="button" @click="filter = 'all'" :class="filter === 'all' ? 'bg-brand text-white shadow-md shadow-brand/25' : 'bg-armor2/70 text-ink/60 hover:bg-surface2'" class="rounded-full px-4 py-1.5 text-sm font-bold transition">Все слова</button>
                <button type="button" @click="filter = 'learned'" :class="filter === 'learned' ? 'bg-brand text-white shadow-md shadow-brand/25' : 'bg-armor2/70 text-ink/60 hover:bg-surface2'" class="rounded-full px-4 py-1.5 text-sm font-bold transition">Выучено</button>
                <button type="button" @click="filter = 'new'" :class="filter === 'new' ? 'bg-brand text-white shadow-md shadow-brand/25' : 'bg-armor2/70 text-ink/60 hover:bg-surface2'" class="rounded-full px-4 py-1.5 text-sm font-bold transition">На изучении</button>
            </div>

            {{-- ===== Список ===== --}}
            <div x-show="mode === 'list'" class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($words as $word)
                    @php $isLearned = in_array($word->id, $learnedWordIds, true); @endphp
                    <div x-show="filter === 'all' || (filter === 'learned') === {{ $isLearned ? 'true' : 'false' }}">
                        @include('public.words._word-card', ['word' => $word, 'isLearned' => $isLearned])
                    </div>
                @endforeach
            </div>

            {{-- ===== Карточки ===== --}}
            <div x-show="mode === 'cards'" style="display:none" x-cloak>
                <template x-if="!current">
                    <p class="py-16 text-center text-ink/50">В этом фильтре пока нет слов.</p>
                </template>

                <template x-if="current">
                    <div class="mx-auto max-w-md">
                        <div class="flip-card h-72 cursor-pointer" :class="{ 'is-flipped': flipped }" @click="flip()">
                            <div class="flip-card-inner h-full w-full">
                                <div class="flip-card-face flex h-full flex-col items-center justify-center rounded-2xl border border-line bg-armor2 p-8 text-center shadow-softLg">
                                    <p class="text-xs font-bold uppercase tracking-widest text-ink/40">Слово</p>
                                    <p class="mt-3 flex items-center gap-2 text-3xl font-extrabold capitalize text-ink">
                                        <span x-text="current.word"></span>
                                        <button type="button" @click.stop="pronounce(current.word, null)" class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-brand/10 text-brand transition hover:bg-brand hover:text-white active:scale-90" aria-label="Прослушать произношение">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="15" height="15"><polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5" /><path d="M15.54 8.46a5 5 0 0 1 0 7.07" /><path d="M19.07 4.93a10 10 0 0 1 0 14.14" /></svg>
                                        </button>
                                    </p>
                                    <p class="mt-2 text-ink/40" x-show="current.transcription" x-text="'/' + current.transcription + '/'"></p>
                                    <p class="mt-6 text-xs text-ink/30">Нажмите, чтобы перевернуть</p>
                                </div>
                                <div class="flip-card-face flip-card-back flex h-full flex-col items-center justify-center rounded-2xl bg-brand p-8 text-center text-white shadow-softLg">
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

            {{-- ===== Тест ===== --}}
            <div x-show="mode === 'quiz'" style="display:none" x-cloak>
                <template x-if="quizPool.length < 4">
                    <p class="py-16 text-center text-ink/50">Нужно минимум 4 слова с переводом, чтобы пройти тест.</p>
                </template>

                <template x-if="quizPool.length >= 4 && quizQuestions.length && !quizDone">
                    <div class="mx-auto max-w-lg">
                        <div class="mb-4 flex items-center justify-between text-sm font-semibold text-ink/40">
                            <span x-text="(quizIndex + 1) + ' / ' + quizQuestions.length"></span>
                            <span x-text="'Правильно: ' + quizScore"></span>
                        </div>

                        <div class="rounded-2xl border border-line bg-armor2 p-8 text-center shadow-soft">
                            <p class="text-xs font-bold uppercase tracking-widest text-ink/40">Как переводится?</p>
                            <p class="mt-3 text-2xl font-extrabold capitalize text-ink" x-text="quizCurrent && quizCurrent.word"></p>

                            <div class="mt-6 grid gap-2.5">
                                <template x-for="option in (quizCurrent ? quizCurrent.options : [])" :key="option">
                                    <button
                                        type="button"
                                        @click="answerQuiz(option)"
                                        :disabled="quizAnswered"
                                        :class="{
                                            'border-green-500 bg-green-500/10 text-green-600 dark:text-green-400': quizAnswered && option === quizCurrent.correct,
                                            'border-red-500 bg-red-500/10 text-red-600 dark:text-red-400': quizAnswered && option === quizSelected && option !== quizCurrent.correct,
                                            'border-line bg-armor text-ink/40': quizAnswered && option !== quizSelected && option !== quizCurrent.correct,
                                            'border-line bg-armor text-ink hover:border-brand/40 hover:bg-brand/5': !quizAnswered,
                                        }"
                                        class="rounded-xl border-2 px-4 py-3 text-left font-semibold transition"
                                        x-text="option"
                                    ></button>
                                </template>
                            </div>

                            <x-ui.button variant="primary" class="mt-6" x-show="quizAnswered" style="display:none" @click="nextQuiz()">Далее</x-ui.button>
                        </div>
                    </div>
                </template>

                <template x-if="quizPool.length >= 4 && quizDone">
                    <div class="mx-auto max-w-md text-center">
                        <div class="rounded-2xl border border-line bg-armor2 p-10 shadow-soft">
                            <p class="text-5xl font-extrabold text-brand" x-text="quizScore + ' / ' + quizQuestions.length"></p>
                            <p class="mt-2 text-ink/60">правильных ответов</p>
                            <x-ui.button variant="primary" class="mt-6" @click="startQuiz()">Пройти ещё раз</x-ui.button>
                        </div>
                    </div>
                </template>
            </div>
        @endif
    </div>
@endsection
