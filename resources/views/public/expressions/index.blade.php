{{-- Выражения (идиомы, фразовые глаголы, пословицы, коллокации). Точное
     зеркало слов/index.blade.php: без категории в URL — сетка карточек-тем,
     внутри темы — список/карточки с переворотом, «Знаю»/«Повторить».
     Дополнительно: фильтр по типу и уровню, поиск по тексту. --}}
@extends('layouts.app')

@section('title', 'Выражения')
@section('page_title', 'Выражения')
@section('meta_description', 'Идиомы, фразовые глаголы, пословицы и коллокации английского языка — список и карточки с переворотом, для Philip Education.')

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
    <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">

        @if (! $selectedCategory)
            {{-- ===== Выбор темы (карточки) ===== --}}
            <div class="mb-8" data-reveal>
                <h1 class="text-3xl font-extrabold text-ink sm:text-4xl">Выражения</h1>
                <p class="mt-1 text-ink/60">Идиомы, фразовые глаголы, пословицы и коллокации — выберите тему, чтобы начать, всего {{ $categoryCounts->sum() }} выражений в {{ $categoryCounts->count() }} темах.</p>
            </div>

            @php
                // 1 выражение / 2 выражения / 5 выражений
                $exprPlural = function (int $n): string {
                    $mod100 = $n % 100;
                    $mod10 = $n % 10;
                    if ($mod100 >= 11 && $mod100 <= 14) return 'выражений';
                    if ($mod10 === 1) return 'выражение';
                    if ($mod10 >= 2 && $mod10 <= 4) return 'выражения';
                    return 'выражений';
                };
            @endphp

            <div class="grid gap-5 sm:grid-cols-2">
                <a
                    href="{{ route('expressions.index', ['category' => 'all']) }}"
                    class="card-lift group relative block overflow-hidden rounded-2xl border border-line bg-armor2 shadow-soft before:absolute before:inset-x-0 before:top-0 before:z-10 before:h-[3px] before:origin-left before:scale-x-0 before:bg-gradient-to-r before:from-[#C9A961] before:to-[#D4AF37] before:transition-transform before:duration-300 before:content-[''] hover:border-brand/30 hover:before:scale-x-100"
                    data-reveal
                >
                    <div class="flex items-center justify-center bg-navy px-6 py-12 sm:py-14">
                        <h2 class="text-center font-display text-xl font-semibold leading-snug text-white sm:text-2xl">Все выражения</h2>
                    </div>
                    <div class="flex items-center justify-between gap-4 border-t border-line px-6 py-4">
                        <span class="inline-flex items-center gap-2 text-sm font-semibold text-ink/60">
                            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-brand"><rect x="3" y="3" width="7" height="7" rx="1.5" /><rect x="14" y="3" width="7" height="7" rx="1.5" /><rect x="3" y="14" width="7" height="7" rx="1.5" /><rect x="14" y="14" width="7" height="7" rx="1.5" /></svg>
                            {{ $categoryCounts->sum() }} {{ $exprPlural($categoryCounts->sum()) }}
                        </span>
                        <span class="inline-flex items-center gap-1.5 text-xs font-bold uppercase tracking-wider text-brand">
                            Открыть
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="transition group-hover:translate-x-1"><path d="M5 12h14M13 5l7 7-7 7" /></svg>
                        </span>
                    </div>
                </a>

                @foreach ($categoryCounts as $cat => $count)
                    <a
                        href="{{ route('expressions.index', ['category' => $cat]) }}"
                        class="card-lift group relative block overflow-hidden rounded-2xl border border-line bg-armor2 shadow-soft before:absolute before:inset-x-0 before:top-0 before:z-10 before:h-[3px] before:origin-left before:scale-x-0 before:bg-gradient-to-r before:from-[#C9A961] before:to-[#D4AF37] before:transition-transform before:duration-300 before:content-[''] hover:border-brand/30 hover:before:scale-x-100"
                        data-reveal
                    >
                        <div class="flex items-center justify-center bg-surface2 px-6 py-12 sm:py-14">
                            <h2 class="text-center font-display text-xl font-semibold leading-snug text-ink sm:text-2xl">{{ $cat }}</h2>
                        </div>
                        <div class="flex items-center justify-between gap-4 border-t border-line px-6 py-4">
                            <span class="inline-flex items-center gap-2 text-sm font-semibold text-ink/60">
                                <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-brand"><path d="M20.59 13.41 13.42 20.6a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82Z" /><circle cx="7" cy="7" r="1.2" fill="currentColor" stroke="none" /></svg>
                                {{ $count }} {{ $exprPlural($count) }}
                            </span>
                            <span class="inline-flex items-center gap-1.5 text-xs font-bold uppercase tracking-wider text-brand">
                                Открыть
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="transition group-hover:translate-x-1"><path d="M5 12h14M13 5l7 7-7 7" /></svg>
                            </span>
                        </div>
                    </a>
                @endforeach
            </div>
        @else
            {{-- ===== Список выражений выбранной темы ===== --}}
            @php
                $flashcards = $expressions->map(fn ($e) => [
                    'id' => $e->id,
                    'word' => $e->text,
                    'transcription' => $e->transcription,
                    'translation' => $e->translations->pluck('translation')->join(', ') ?: '—',
                    'learned' => in_array($e->id, $learnedExpressionIds, true),
                ])->values();
            @endphp

            <div x-data="Object.assign(flashcardDeck({{ Js::from($flashcards) }}, '/expressions'), expressionQuiz({{ Js::from($flashcards) }}), { mode: 'list', filter: 'all', addOpen: false })">
                <a href="{{ route('expressions.index') }}" class="mb-4 inline-flex items-center gap-1 text-sm font-semibold text-ink/60 transition hover:text-brand" data-reveal>
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 12H5M11 18l-6-6 6-6" /></svg>
                    Все темы
                </a>

                <div class="mb-6 flex flex-wrap items-end justify-between gap-4" data-reveal>
                    <div>
                        <h1 class="text-3xl font-extrabold text-ink sm:text-4xl">{{ $selectedCategory === 'all' ? 'Все выражения' : $selectedCategory }}</h1>
                        <p class="mt-1 text-ink/60">{{ $expressions->count() }} выражений — {{ count($learnedExpressionIds) }} уже выучено.</p>
                    </div>

                    <div class="flex items-center gap-2">
                        <x-ui.button variant="outline" size="sm" @click="addOpen = true">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 5v14M5 12h14" /></svg>
                            Добавить своё выражение
                        </x-ui.button>

                        <div class="flex rounded-xl border border-line bg-armor2/70 p-1">
                            <button type="button" @click="mode = 'list'" :class="mode === 'list' ? 'bg-brand text-white shadow' : 'text-ink/60'" class="rounded-lg px-3 py-1.5 text-sm font-bold transition">Список</button>
                            <button type="button" @click="mode = 'cards'; index = 0; flipped = false" :class="mode === 'cards' ? 'bg-brand text-white shadow' : 'text-ink/60'" class="rounded-lg px-3 py-1.5 text-sm font-bold transition">Карточки</button>
                            <button type="button" @click="mode = 'quiz'; startQuiz()" :class="mode === 'quiz' ? 'bg-brand text-white shadow' : 'text-ink/60'" class="rounded-lg px-3 py-1.5 text-sm font-bold transition">Проверь себя</button>
                        </div>
                    </div>
                </div>

                {{-- Фильтр по типу, уровню, поиск — серверные (GET), сохраняют текущую тему --}}
                <form method="GET" action="{{ route('expressions.index') }}" class="mb-4 flex flex-wrap items-center gap-2" data-reveal>
                    <input type="hidden" name="category" value="{{ $selectedCategory }}">

                    <a
                        href="{{ route('expressions.index', ['category' => $selectedCategory]) }}"
                        class="rounded-full px-4 py-1.5 text-sm font-bold transition {{ ! $selectedType ? 'bg-brand text-white shadow-md shadow-brand/25' : 'bg-armor2/70 text-ink/60 hover:bg-surface2' }}"
                    >Все типы</a>
                    @foreach ($types as $typeKey => $typeLabel)
                        <a
                            href="{{ route('expressions.index', ['category' => $selectedCategory, 'type' => $typeKey, 'level' => $selectedLevel, 'search' => $search ?: null]) }}"
                            class="rounded-full px-4 py-1.5 text-sm font-bold transition {{ $selectedType === $typeKey ? 'bg-brand text-white shadow-md shadow-brand/25' : 'bg-armor2/70 text-ink/60 hover:bg-surface2' }}"
                        >{{ $typeLabel }}</a>
                    @endforeach

                    <select name="level" onchange="this.form.submit()" class="rounded-full border border-line bg-armor2/70 px-4 py-1.5 text-sm font-bold text-ink/70">
                        <option value="">Любой уровень</option>
                        @foreach (['A1', 'A2', 'B1', 'B2', 'C1', 'C2'] as $level)
                            <option value="{{ $level }}" @selected($selectedLevel === $level)>{{ $level }}</option>
                        @endforeach
                    </select>

                    <input type="hidden" name="type" value="{{ $selectedType }}">
                    <input
                        type="search"
                        name="search"
                        value="{{ $search }}"
                        placeholder="Поиск по тексту…"
                        class="min-w-[180px] flex-1 rounded-full border border-line bg-armor2/70 px-4 py-1.5 text-sm text-ink placeholder:text-ink/40 focus:border-brand/40 focus:outline-none"
                    >
                    <button type="submit" class="rounded-full bg-brand px-4 py-1.5 text-sm font-bold text-white transition hover:bg-brand/90">Найти</button>
                </form>

                {{-- Фильтр по статусу изучения (клиентский, Alpine) --}}
                <div class="mb-8 flex flex-wrap gap-2" data-reveal>
                    <button type="button" @click="filter = 'all'" :class="filter === 'all' ? 'bg-brand text-white shadow-md shadow-brand/25' : 'bg-armor2/70 text-ink/60 hover:bg-surface2'" class="rounded-full px-4 py-1.5 text-sm font-bold transition">Все выражения</button>
                    <button type="button" @click="filter = 'learned'" :class="filter === 'learned' ? 'bg-brand text-white shadow-md shadow-brand/25' : 'bg-armor2/70 text-ink/60 hover:bg-surface2'" class="rounded-full px-4 py-1.5 text-sm font-bold transition">Выучено</button>
                    <button type="button" @click="filter = 'new'" :class="filter === 'new' ? 'bg-brand text-white shadow-md shadow-brand/25' : 'bg-armor2/70 text-ink/60 hover:bg-surface2'" class="rounded-full px-4 py-1.5 text-sm font-bold transition">На изучении</button>
                </div>

                @if ($expressions->isEmpty())
                    <x-ui.card :hover="false" class="py-16 text-center">
                        <p class="text-lg font-semibold text-ink">Выражений пока нет</p>
                        <p class="mt-1 text-ink/50">Попробуйте изменить фильтры или добавьте своё первое выражение кнопкой выше.</p>
                    </x-ui.card>
                @else
                    {{-- ===== Список ===== --}}
                    <div x-show="mode === 'list'" class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                        @foreach ($expressions as $expression)
                            @php $isLearned = in_array($expression->id, $learnedExpressionIds, true); @endphp
                            <div x-show="filter === 'all' || (filter === 'learned') === {{ $isLearned ? 'true' : 'false' }}">
                                <a
                                    href="{{ route('expressions.show', $expression->id) }}"
                                    class="group flex h-full flex-col rounded-2xl border border-line bg-armor2/70 p-5 shadow-lg shadow-ink/5 backdrop-blur-xl transition duration-300 ease-out hover:-translate-y-1.5 hover:border-brand/30 hover:shadow-2xl hover:shadow-brand/15"
                                    data-reveal
                                >
                                    <div class="mb-2 flex items-start justify-between gap-2">
                                        <div>
                                            <h3 class="flex items-center gap-1.5 text-lg font-bold text-ink">
                                                {{ $expression->text }}
                                                <x-speak-button :word="$expression->text" :audio-url="$expression->audio_url" />
                                            </h3>
                                            @if ($expression->transcription)
                                                <span class="text-sm text-ink/40">/{{ $expression->transcription }}/</span>
                                            @endif
                                        </div>
                                        <x-ui.badge :variant="$isLearned ? 'success' : 'neutral'">{{ $isLearned ? 'Выучено' : 'Новое' }}</x-ui.badge>
                                    </div>

                                    <div class="mb-1 flex items-center gap-2">
                                        <x-ui.badge variant="accent">{{ $types[$expression->type] ?? $expression->type }}</x-ui.badge>
                                        @if ($expression->level)
                                            <x-ui.badge variant="level" :level="$expression->level" />
                                        @endif
                                    </div>

                                    @if ($expression->translations->isNotEmpty())
                                        <p class="font-semibold text-brand">{{ $expression->translations->pluck('translation')->join(', ') }}</p>
                                    @endif

                                    @if ($expression->example)
                                        <p class="mt-2 flex-1 text-sm italic text-ink/50">&laquo;{{ $expression->example }}&raquo;</p>
                                    @endif

                                    @if ($selectedCategory === 'all' && $expression->category)
                                        <span class="mt-3 inline-flex w-fit items-center rounded-full bg-sun/10 border border-sun/30 px-2.5 py-0.5 text-xs font-bold text-sun">{{ $expression->category }}</span>
                                    @endif
                                </a>
                            </div>
                        @endforeach
                    </div>

                    {{-- ===== Карточки (флеш-карты) ===== --}}
                    <div x-show="mode === 'cards'" style="display:none" x-cloak>
                        <template x-if="!current">
                            <p class="py-16 text-center text-ink/50">В этом фильтре пока нет выражений.</p>
                        </template>

                        <template x-if="current">
                            <div class="mx-auto max-w-md" data-reveal>
                                <div
                                    class="flip-card h-72 cursor-pointer"
                                    :class="{ 'is-flipped': flipped }"
                                    @click="flip()"
                                >
                                    <div class="flip-card-inner h-full w-full">
                                        <div class="flip-card-face flex h-full flex-col items-center justify-center rounded-2xl border border-line bg-armor2 p-8 text-center shadow-softLg">
                                            <p class="text-xs font-bold uppercase tracking-widest text-ink/40">Выражение</p>
                                            <p class="mt-3 flex items-center gap-2 text-2xl font-extrabold text-ink">
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

                    {{-- ===== Проверь себя (квиз) ===== --}}
                    <div x-show="mode === 'quiz'" style="display:none" x-cloak>
                        <template x-if="quizPool.length < 4">
                            <p class="py-16 text-center text-ink/50">Нужно минимум 4 выражения с переводом, чтобы пройти квиз.</p>
                        </template>

                        <template x-if="quizPool.length >= 4 && quizQuestions.length && !quizDone">
                            <div class="mx-auto max-w-lg" data-reveal>
                                <div class="mb-4 flex items-center justify-between text-sm font-semibold text-ink/40">
                                    <span x-text="(quizIndex + 1) + ' / ' + quizQuestions.length"></span>
                                    <span x-text="'Правильно: ' + quizScore"></span>
                                </div>

                                <div class="rounded-2xl border border-line bg-armor2 p-8 text-center shadow-soft">
                                    <p class="text-xs font-bold uppercase tracking-widest text-ink/40">Как переводится?</p>
                                    <p class="mt-3 text-2xl font-extrabold text-ink" x-text="quizCurrent && quizCurrent.word"></p>

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

                                    <x-ui.button variant="primary" class="mt-6" x-show="quizAnswered" style="display:none" @click="nextQuiz()">
                                        Далее
                                    </x-ui.button>
                                </div>
                            </div>
                        </template>

                        <template x-if="quizPool.length >= 4 && quizDone">
                            <div class="mx-auto max-w-md text-center" data-reveal>
                                <div class="rounded-2xl border border-line bg-armor2 p-10 shadow-soft">
                                    <p class="text-5xl font-extrabold text-brand" x-text="quizScore + ' / ' + quizQuestions.length"></p>
                                    <p class="mt-2 text-ink/60">правильных ответов</p>
                                    <x-ui.button variant="primary" class="mt-6" @click="startQuiz()">Пройти ещё раз</x-ui.button>
                                </div>
                            </div>
                        </template>
                    </div>
                @endif

                {{-- ===== Модалка «Добавить своё выражение» ===== --}}
                <div
                    x-show="addOpen"
                    style="display:none"
                    x-cloak
                    class="fixed inset-0 z-[60] flex items-center justify-center bg-ink/40 p-4 backdrop-blur-sm"
                    @keydown.escape.window="addOpen = false"
                >
                    <div @click.outside="addOpen = false" class="w-full max-w-md rounded-2xl border border-line bg-armor2 p-6 shadow-2xl">
                        <div class="mb-4 flex items-center justify-between">
                            <h2 class="text-lg font-bold text-ink">Добавить своё выражение</h2>
                            <button type="button" @click="addOpen = false" class="text-ink/40 hover:text-ink"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6 6 18M6 6l12 12" /></svg></button>
                        </div>
                        <form method="POST" action="{{ route('expressions.store') }}" class="space-y-4">
                            @csrf
                            <x-ui.input name="text" label="Выражение (на английском)" placeholder="например, break the ice" required />

                            <div>
                                <label class="mb-1 block text-sm font-semibold text-ink/70">Тип</label>
                                <select name="type" required class="w-full rounded-xl border border-line bg-armor2/70 px-4 py-2.5 text-ink">
                                    @foreach ($types as $typeKey => $typeLabel)
                                        <option value="{{ $typeKey }}">{{ $typeLabel }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <x-ui.input name="translation" label="Перевод" placeholder="например, растопить лёд" required />
                            <x-ui.input name="meaning" label="Значение на английском (необязательно)" placeholder="To relieve tension in an awkward situation." />
                            <x-ui.input name="example" label="Пример предложения (необязательно)" placeholder="He told a joke to break the ice." />
                            <x-ui.button type="submit" variant="primary" class="w-full">Добавить выражение</x-ui.button>
                        </form>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection
