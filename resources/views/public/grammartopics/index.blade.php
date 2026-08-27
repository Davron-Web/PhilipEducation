{{-- Грамматика. Без категории в URL — сетка карточек-тем, переход по клику
     открывает список тем этой категории (с фильтром по уровню внутри). --}}
@extends('layouts.app')

@section('title', 'Грамматика')
@section('page_title', 'Темы по грамматике')
@section('meta_description', 'Темы по грамматике английского языка от A1 до C1 с объяснениями и примерами.')

@section('content')
    <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">

        @if (! $selectedCategory)
            {{-- ===== Выбор категории (карточки) ===== --}}
            <div class="mb-8" data-reveal>
                <h1 class="text-3xl font-extrabold text-ink sm:text-4xl">Грамматика</h1>
                <p class="mt-1 text-ink/60">Выберите категорию — всего {{ $categoryCounts->sum() }} тем от базовых правил до продвинутых конструкций.</p>
            </div>

            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                <a
                    href="{{ route('grammartopics.index', ['category' => 'all']) }}"
                    class="group flex items-center gap-3 rounded-2xl border border-white/60 bg-gradient-to-br from-brand to-sky p-5 text-white shadow-lg shadow-brand/20 transition duration-300 ease-out hover:-translate-y-1.5 hover:shadow-2xl"
                    data-reveal
                >
                    <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-white/20">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7" rx="1.5" /><rect x="14" y="3" width="7" height="7" rx="1.5" /><rect x="3" y="14" width="7" height="7" rx="1.5" /><rect x="14" y="14" width="7" height="7" rx="1.5" /></svg>
                    </span>
                    <span>
                        <span class="block font-bold">Все темы</span>
                        <span class="block text-sm text-white/70">{{ $categoryCounts->sum() }} тем</span>
                    </span>
                </a>

                @foreach ($categoryCounts as $cat => $count)
                    <a
                        href="{{ route('grammartopics.index', ['category' => $cat]) }}"
                        class="group flex items-center gap-3 rounded-2xl border border-white/60 bg-gradient-to-br from-brand to-sky p-5 text-white shadow-lg shadow-brand/20 transition duration-300 ease-out hover:-translate-y-1.5 hover:shadow-2xl"
                        data-reveal
                    >
                        <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-white/20">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20M4 19.5A2.5 2.5 0 0 0 6.5 22H20V4H6.5A2.5 2.5 0 0 0 4 6.5v13Z" /></svg>
                        </span>
                        <span>
                            <span class="block font-bold">{{ $cat }}</span>
                            <span class="block text-sm text-white/70">{{ $count }} {{ $count === 1 ? 'тема' : 'тем' }}</span>
                        </span>
                    </a>
                @endforeach
            </div>
        @else
            {{-- ===== Темы выбранной категории ===== --}}
            <div x-data="{ level: 'all' }">
                <a href="{{ route('grammartopics.index') }}" class="mb-4 inline-flex items-center gap-1 text-sm font-semibold text-ink/60 transition hover:text-brand" data-reveal>
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 12H5M11 18l-6-6 6-6" /></svg>
                    Все категории
                </a>

                <div class="mb-8" data-reveal>
                    <h1 class="text-3xl font-extrabold text-ink sm:text-4xl">{{ $selectedCategory === 'all' ? 'Все темы' : $selectedCategory }}</h1>
                    <p class="mt-1 text-ink/60">{{ $topics->count() }} {{ $topics->count() === 1 ? 'тема' : 'тем' }}</p>
                </div>

                @if ($topics->isEmpty())
                    <x-ui.card :hover="false" class="py-16 text-center">
                        <p class="text-lg font-semibold text-ink">Темы пока не опубликованы</p>
                        <p class="mt-1 text-ink/50">Загляните позже — мы уже готовим материалы.</p>
                    </x-ui.card>
                @else
                    @php
                        $levels = $topics->map(fn ($t) => optional($t->level)->code)->filter()->unique()->sort()->values();
                    @endphp

                    @if ($levels->isNotEmpty())
                        <div class="mb-8 flex flex-wrap gap-2" data-reveal>
                            <button
                                type="button"
                                @click="level = 'all'"
                                :class="level === 'all' ? 'bg-brand text-white shadow-md shadow-brand/25' : 'bg-armor2/70 text-ink/60 hover:bg-white/5'"
                                class="rounded-full px-4 py-1.5 text-sm font-bold transition"
                            >Все уровни</button>
                            @foreach ($levels as $code)
                                <button
                                    type="button"
                                    @click="level = '{{ $code }}'"
                                    :class="level === '{{ $code }}' ? 'bg-brand text-white shadow-md shadow-brand/25' : 'bg-armor2/70 text-ink/60 hover:bg-white/5'"
                                    class="rounded-full px-4 py-1.5 text-sm font-bold transition"
                                >{{ $code }}</button>
                            @endforeach
                        </div>
                    @endif

                    <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                        @foreach ($topics as $topic)
                            @php $code = optional($topic->level)->code ?? 'General'; @endphp
                            <div x-show="level === 'all' || level === '{{ $code }}'">
                                <a
                                    href="{{ route('grammartopics.show', $topic->id) }}"
                                    class="group flex h-full flex-col rounded-2xl border border-white/10 bg-armor2/70 p-5 shadow-lg shadow-ink/5 backdrop-blur-xl transition duration-300 ease-out hover:-translate-y-1.5 hover:border-brand/30 hover:shadow-2xl hover:shadow-brand/15"
                                    data-reveal
                                >
                                    <x-ui.badge variant="level" :level="$topic->level" class="mb-3 self-start" />
                                    <h2 class="text-lg font-bold text-ink">{{ $topic->title }}</h2>
                                    <p class="mt-2 line-clamp-3 flex-1 text-sm text-ink/60">{{ Str::limit(strip_tags($topic->theory), 130) }}</p>
                                    <span class="mt-4 inline-flex items-center gap-1 text-sm font-bold text-brand">
                                        Изучить
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" class="transition-transform group-hover:translate-x-1"><path d="M5 12h14M13 6l6 6-6 6" /></svg>
                                    </span>
                                </a>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        @endif
    </div>
@endsection
