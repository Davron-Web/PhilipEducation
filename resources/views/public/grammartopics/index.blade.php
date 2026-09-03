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

            @php
                // 1 тема / 2 темы / 5 тем
                $topicsPlural = function (int $n): string {
                    $mod100 = $n % 100;
                    $mod10 = $n % 10;
                    if ($mod100 >= 11 && $mod100 <= 14) return 'тем';
                    if ($mod10 === 1) return 'тема';
                    if ($mod10 >= 2 && $mod10 <= 4) return 'темы';
                    return 'тем';
                };
            @endphp

            <div class="grid gap-5 sm:grid-cols-2">
                <a
                    href="{{ route('grammartopics.index', ['category' => 'all']) }}"
                    class="card-lift group relative block overflow-hidden rounded-2xl border border-line bg-armor2 shadow-soft before:absolute before:inset-x-0 before:top-0 before:z-10 before:h-[3px] before:origin-left before:scale-x-0 before:bg-gradient-to-r before:from-[#C9A961] before:to-[#D4AF37] before:transition-transform before:duration-300 before:content-[''] hover:border-brand/30 hover:before:scale-x-100"
                    data-reveal
                >
                    <div class="flex items-center justify-center bg-navy px-6 py-12 sm:py-14">
                        <h2 class="text-center font-display text-xl font-semibold leading-snug text-white sm:text-2xl">Все темы</h2>
                    </div>
                    <div class="flex items-center justify-between gap-4 border-t border-line px-6 py-4">
                        <span class="inline-flex items-center gap-2 text-sm font-semibold text-ink/60">
                            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-brand"><rect x="3" y="3" width="7" height="7" rx="1.5" /><rect x="14" y="3" width="7" height="7" rx="1.5" /><rect x="3" y="14" width="7" height="7" rx="1.5" /><rect x="14" y="14" width="7" height="7" rx="1.5" /></svg>
                            {{ $categoryCounts->sum() }} {{ $topicsPlural($categoryCounts->sum()) }}
                        </span>
                        <span class="inline-flex items-center gap-1.5 text-xs font-bold uppercase tracking-wider text-brand">
                            {{ __('site.common.open') }}
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="transition group-hover:translate-x-1"><path d="M5 12h14M13 5l7 7-7 7" /></svg>
                        </span>
                    </div>
                </a>

                @foreach ($categoryCounts as $cat => $count)
                    <a
                        href="{{ route('grammartopics.index', ['category' => $cat]) }}"
                        class="card-lift group relative block overflow-hidden rounded-2xl border border-line bg-armor2 shadow-soft before:absolute before:inset-x-0 before:top-0 before:z-10 before:h-[3px] before:origin-left before:scale-x-0 before:bg-gradient-to-r before:from-[#C9A961] before:to-[#D4AF37] before:transition-transform before:duration-300 before:content-[''] hover:border-brand/30 hover:before:scale-x-100"
                        data-reveal
                    >
                        <div class="flex items-center justify-center bg-surface2 px-6 py-12 sm:py-14">
                            <h2 class="text-center font-display text-xl font-semibold leading-snug text-ink sm:text-2xl">{{ $cat }}</h2>
                        </div>
                        <div class="flex items-center justify-between gap-4 border-t border-line px-6 py-4">
                            <span class="inline-flex items-center gap-2 text-sm font-semibold text-ink/60">
                                <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-brand"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20M4 19.5A2.5 2.5 0 0 0 6.5 22H20V4H6.5A2.5 2.5 0 0 0 4 6.5v13Z" /></svg>
                                {{ $count }} {{ $topicsPlural($count) }}
                            </span>
                            <span class="inline-flex items-center gap-1.5 text-xs font-bold uppercase tracking-wider text-brand">
                                {{ __('site.common.open') }}
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="transition group-hover:translate-x-1"><path d="M5 12h14M13 5l7 7-7 7" /></svg>
                            </span>
                        </div>
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
                                :class="level === 'all' ? 'bg-brand text-white shadow-md shadow-brand/25' : 'bg-armor2/70 text-ink/60 hover:bg-surface2'"
                                class="rounded-full px-4 py-1.5 text-sm font-bold transition"
                            >Все уровни</button>
                            @foreach ($levels as $code)
                                <button
                                    type="button"
                                    @click="level = '{{ $code }}'"
                                    :class="level === '{{ $code }}' ? 'bg-brand text-white shadow-md shadow-brand/25' : 'bg-armor2/70 text-ink/60 hover:bg-surface2'"
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
                                    class="group flex h-full flex-col rounded-2xl border border-line bg-armor2/70 p-5 shadow-lg shadow-ink/5 backdrop-blur-xl transition duration-300 ease-out hover:-translate-y-1.5 hover:border-brand/30 hover:shadow-2xl hover:shadow-brand/15"
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
