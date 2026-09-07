{{-- Гостевая главная страница. Использует общий layout (layouts.app) —
     шапку, подвал и виджет Phil со всего сайта — чтобы дизайн был
     полностью единым, а не отдельной «одноразовой» страницей.
     Тема "Philip Elite": тёмно-синий + золото, антиква в заголовках. --}}
@extends('layouts.app')

@section('title', 'Philip Education')
@section('page_title', __('site.meta.home_title'))
@section('meta_description', __('site.meta.home_description'))

@push('styles')
    <style>
        .flip-card { perspective: 1200px; }
        .flip-card-inner {
            position: relative;
            transform-style: preserve-3d;
            transition: transform .5s cubic-bezier(.22,1,.36,1);
        }
        .flip-card.is-flipped .flip-card-inner { transform: rotateY(180deg); }
        .flip-face { backface-visibility: hidden; -webkit-backface-visibility: hidden; }
        .flip-back { position: absolute; inset: 0; transform: rotateY(180deg); }

        .hero-mesh {
            background:
                radial-gradient(70% 60% at 85% 0%, rgb(201 169 97 / .10), transparent 60%),
                radial-gradient(50% 50% at 10% 100%, rgb(201 169 97 / .06), transparent 60%);
        }
    </style>
@endpush

@section('content')

    {{-- ===================== HERO (всегда тёмно-синий) ===================== --}}
    <section class="hero-mesh relative overflow-hidden bg-navy">
        <div class="mx-auto grid max-w-7xl items-center gap-12 px-4 py-20 sm:px-6 lg:grid-cols-2 lg:px-8 lg:py-28">
            <div data-reveal>
                <span class="inline-flex items-center gap-2 rounded-full border border-gold/30 bg-gold/10 px-3.5 py-1.5 text-[12px] font-bold uppercase tracking-wider text-gold">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M11 2 3 14h7l-1 8 9-13h-7l1-7Z" /></svg>
                    {{ __('site.home.badge') }}
                </span>

                <h1 class="mt-6 font-display text-[38px] font-semibold leading-[1.15] text-white sm:text-[48px] lg:text-[54px]">
                    {{ __("site.home.hero_title_1") }}<br>
                    <span class="text-gold">{{ __('site.home.hero_title_accent') }}</span> {{ __("site.home.hero_title_2") }}
                </h1>

                <p class="mt-5 max-w-xl text-[17px] leading-relaxed text-white/65">
                    {{ __('site.home.hero_text') }}
                </p>

                <div class="mt-9 flex flex-wrap items-center gap-4">
                    <x-ui.button :href="route('register')" size="lg">
                        {{ __('site.home.cta_start') }}
                    </x-ui.button>
                    <a href="#lessons" class="inline-flex items-center gap-2 rounded border-2 border-white/25 px-8 py-3 text-xs font-bold uppercase tracking-wider text-white transition hover:border-gold hover:text-gold">
                        {{ __('site.home.cta_levels') }}
                    </a>
                </div>

                <div class="mt-12 flex flex-wrap items-center gap-x-8 gap-y-3 border-t border-white/10 pt-6 text-[13px] text-white/50">
                    <div><span class="font-display text-xl font-semibold text-gold" data-counter data-target="{{ $totalUsers }}">0</span> {{ __('site.home.stat_students') }}</div>
                    <div><span class="font-display text-xl font-semibold text-gold" data-counter data-target="{{ $totalWords }}">0</span> {{ __('site.home.stat_words') }}</div>
                    <div><span class="font-display text-xl font-semibold text-gold" data-counter data-target="{{ $totalLessons }}">0</span> {{ __('site.home.stat_lessons') }}</div>
                </div>
            </div>

            {{-- Витрина «слово дня» — светлая карточка на тёмном фоне, как в оригинальном шаблоне --}}
            <div class="relative hidden lg:block" data-reveal>
                <div class="overflow-hidden rounded-2xl border border-line bg-armor2 shadow-2xl">
                    <div class="flex items-center justify-between border-b border-line px-6 py-4">
                        <p class="text-xs font-bold uppercase tracking-wider text-ink/40">{{ __('site.home.word_of_day') }}</p>
                        <span class="rounded bg-gold px-2 py-0.5 text-xs font-bold text-navy">B1</span>
                    </div>
                    <div class="px-6 py-6">
                        <p class="font-display text-3xl font-semibold text-ink">resilient</p>
                        <p class="mt-1 font-mono text-sm text-sky">/rɪˈzɪliənt/</p>
                        <p class="mt-3 text-[15px] font-semibold text-brand">{{ __('site.glossary.resilient') }}</p>
                        <p class="mt-3 text-sm italic leading-relaxed text-ink/50">«Despite the setbacks, she remained resilient.»</p>
                    </div>
                    <div class="border-t border-line">
                        <div class="flex items-center justify-between px-6 py-3 text-sm">
                            <span class="font-semibold text-ink">serendipity</span>
                            <span class="text-ink/40">{{ __('site.glossary.serendipity') }}</span>
                        </div>
                        <div class="flex items-center justify-between border-t border-line px-6 py-3 text-sm">
                            <span class="font-semibold text-ink">eloquent</span>
                            <span class="text-ink/40">{{ __('site.glossary.eloquent') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ===================== РАЗДЕЛЫ ПЛАТФОРМЫ ===================== --}}
    @php
        $sections = [
            ['label' => __('site.nav.lessons'), 'desc' => __('site.home.sections.lessons'), 'route' => 'lessons.index', 'icon' => '<path d="M22 10 12 5 2 10l10 5 10-5Z" /><path d="M6 12v5c0 1.5 3 3 6 3s6-1.5 6-3v-5" />'],
            ['label' => __('site.nav.grammar'), 'desc' => __('site.home.sections.grammar'), 'route' => 'grammartopics.index', 'icon' => '<path d="M12 20h9" /><path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5Z" />'],
            ['label' => __('site.nav.vocabulary'), 'desc' => __('site.home.sections.vocabulary'), 'route' => 'words.index', 'icon' => '<path d="m5 8 6 6" /><path d="m4 14 6-6 2-3" /><path d="M2 5h12" /><path d="m22 22-5-10-5 10" /><path d="M14 18h6" />'],
            ['label' => __('site.nav.expressions'), 'desc' => __('site.home.sections.expressions'), 'route' => 'expressions.index', 'icon' => '<path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z" />'],
            ['label' => __('site.nav.exercises'), 'desc' => __('site.home.sections.exercises'), 'route' => 'exercises.index', 'icon' => '<path d="M14.4 14.4 9.6 9.6" /><path d="M18.657 21.485a2 2 0 1 1-2.829-2.828l6.364-6.364a2 2 0 1 1 2.829 2.829z" /><path d="M6.404 12.768a2 2 0 1 1-2.829-2.829l1.768-1.768a2 2 0 1 1-2.828-2.828l1.768-1.768a2 2 0 1 1 2.828 2.828l-1.768 1.768a2 2 0 1 1 2.829 2.829z" />'],
            ['label' => __('site.nav.tests'), 'desc' => __('site.home.sections.tests'), 'route' => 'tests.index', 'icon' => '<path d="m3 17 2 2 4-4" /><path d="m3 7 2 2 4-4" /><path d="M13 6h8" /><path d="M13 12h8" /><path d="M13 18h8" />'],
            ['label' => __('site.nav.ielts'), 'desc' => __('site.home.sections.ielts'), 'route' => 'ielts.index', 'icon' => '<path d="M12 20h9" /><path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5Z" />'],
            ['label' => __('site.nav.books'), 'desc' => __('site.home.sections.books'), 'route' => 'books.index', 'icon' => '<path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20" /><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2Z" />'],
            ['label' => __('site.nav.achievements'), 'desc' => __('site.home.sections.achievements'), 'route' => 'achievements.index', 'icon' => '<circle cx="12" cy="8" r="6" /><path d="M15.477 12.89 17 22l-5-3-5 3 1.523-9.11" />'],
        ];
    @endphp

    <section id="sections" class="bg-armor py-20 sm:py-28">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="mx-auto max-w-2xl text-center" data-reveal>
                <p class="text-xs font-bold uppercase tracking-[0.2em] text-brand">{{ __('site.home.why_eyebrow') }}</p>
                <h2 class="mt-3 font-display text-[32px] font-semibold text-ink sm:text-[38px]">{{ __('site.home.why_title') }}</h2>
                <div class="mx-auto mt-4 h-[3px] w-14 bg-gradient-to-r from-[#C9A961] to-[#D4AF37]"></div>
                <p class="mt-4 text-[16px] text-ink/60">{{ __('site.home.why_text') }}</p>
            </div>

            <div class="mt-16 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($sections as $s)
                    <a
                        href="{{ route($s['route']) }}"
                        data-reveal
                        class="card-lift group relative overflow-hidden rounded-2xl border border-line bg-armor2 p-8 before:absolute before:inset-x-0 before:top-0 before:h-[3px] before:origin-left before:scale-x-0 before:bg-gradient-to-r before:from-[#C9A961] before:to-[#D4AF37] before:transition-transform before:duration-300 before:content-[''] hover:before:scale-x-100"
                    >
                        <span class="grid h-14 w-14 place-items-center rounded-full bg-brand/10 text-brand">
                            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">{!! $s['icon'] !!}</svg>
                        </span>
                        <h3 class="mt-6 font-display text-[19px] font-semibold text-ink">{{ $s['label'] }}</h3>
                        <p class="mt-2 text-[14.5px] leading-relaxed text-ink/60">{{ $s['desc'] }}</p>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ===================== УРОВНИ (реальные данные, оформлены как «программы») ===================== --}}
    <section id="lessons" class="bg-armor2 py-20 sm:py-28">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="mx-auto max-w-2xl text-center" data-reveal>
                <p class="text-xs font-bold uppercase tracking-[0.2em] text-brand">{{ __('site.home.levels_eyebrow') }}</p>
                <h2 class="mt-3 font-display text-[32px] font-semibold text-ink sm:text-[38px]">{{ __('site.home.levels_title') }}</h2>
                <div class="mx-auto mt-4 h-[3px] w-14 bg-gradient-to-r from-[#C9A961] to-[#D4AF37]"></div>
                <p class="mt-4 text-[16px] text-ink/60">{{ __('site.home.levels_text') }}</p>
            </div>

            <div class="mt-16 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($levels as $level)
                    <div data-reveal class="card-lift overflow-hidden rounded-2xl border border-line shadow-soft">
                        <div class="relative bg-navy px-6 py-8">
                            @if ($level['code'] === 'B1')
                                <span class="absolute right-5 top-5 rounded bg-gold px-2.5 py-1 text-xs font-bold uppercase tracking-wider text-navy">{{ __('site.home.levels_popular') }}</span>
                            @endif
                            <span class="inline-block rounded bg-gold/15 px-3 py-1 text-xs font-bold uppercase tracking-wider text-gold">{{ $level['code'] }}</span>
                            <h3 class="mt-4 font-display text-[22px] font-semibold text-white">{{ $level['name'] }}</h3>
                            <p class="mt-1 text-[13px] text-white/50">{{ $level['lessons_count'] }} {{ $level['lessons_count'] === 1 ? 'урок' : 'уроков' }}</p>
                        </div>
                        <div class="bg-armor2 p-6">
                            <p class="text-[14px] leading-relaxed text-ink/60">{{ \Illuminate\Support\Str::limit($level['description'], 100) }}</p>
                            <a
                                href="{{ route('register') }}"
                                class="mt-5 block rounded border-2 border-brand py-2.5 text-center text-xs font-bold uppercase tracking-wider text-brand transition hover:bg-brand hover:text-white"
                            >{{ __("site.home.levels_start_with", ["code" => $level['code']]) }}</a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ===================== СЛОВАРЬ + ФЛЕШ-КАРТОЧКА ===================== --}}
    <section id="vocabulary" class="bg-armor py-20 sm:py-28">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex flex-wrap items-end justify-between gap-4" data-reveal>
                <div>
                    <p class="text-xs font-bold uppercase tracking-[0.2em] text-brand">{{ __('site.home.vocab_eyebrow') }}</p>
                    <h2 class="mt-3 font-display text-[32px] font-semibold text-ink sm:text-[38px]">{{ __('site.home.vocab_title') }}</h2>
                </div>
                <a href="{{ route('register') }}" class="hidden items-center gap-1.5 text-xs font-bold uppercase tracking-wider text-brand sm:inline-flex">{{ __('site.home.vocab_all') }} <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6"><path d="M5 12h14M13 5l7 7-7 7" /></svg></a>
            </div>

            <div class="mt-12 grid items-center gap-10 lg:grid-cols-[1fr_auto]">
                <div class="grid gap-5 sm:grid-cols-2">
                    <div data-reveal class="card-lift rounded-xl border border-line bg-armor2 p-5">
                        <p class="font-display text-[18px] font-semibold text-ink">ubiquitous</p>
                        <p class="font-mono text-[13px] text-sky">/juːˈbɪkwɪtəs/</p>
                        <p class="mt-2 text-[14px] font-semibold text-brand">{{ __('site.glossary.ubiquitous') }}</p>
                        <p class="mt-2 text-[13.5px] italic leading-relaxed text-ink/50">«Smartphones have become ubiquitous.»</p>
                    </div>
                    <div data-reveal class="card-lift rounded-xl border border-line bg-armor2 p-5">
                        <p class="font-display text-[18px] font-semibold text-ink">meticulous</p>
                        <p class="font-mono text-[13px] text-sky">/məˈtɪkjələs/</p>
                        <p class="mt-2 text-[14px] font-semibold text-brand">{{ __('site.glossary.meticulous') }}</p>
                        <p class="mt-2 text-[13.5px] italic leading-relaxed text-ink/50">«She is meticulous about every detail.»</p>
                    </div>
                    <div data-reveal class="card-lift rounded-xl border border-line bg-armor2 p-5">
                        <p class="font-display text-[18px] font-semibold text-ink">eloquent</p>
                        <p class="font-mono text-[13px] text-sky">/ˈeləkwənt/</p>
                        <p class="mt-2 text-[14px] font-semibold text-brand">{{ __('site.glossary.eloquent') }}</p>
                        <p class="mt-2 text-[13.5px] italic leading-relaxed text-ink/50">«She gave an eloquent speech.»</p>
                    </div>
                    <div data-reveal class="card-lift rounded-xl border border-line bg-armor2 p-5">
                        <p class="font-display text-[18px] font-semibold text-ink">perseverance</p>
                        <p class="font-mono text-[13px] text-sky">/ˌpɜːsəˈvɪərəns/</p>
                        <p class="mt-2 text-[14px] font-semibold text-brand">{{ __('site.glossary.perseverance') }}</p>
                        <p class="mt-2 text-[13.5px] italic leading-relaxed text-ink/50">«Success requires perseverance.»</p>
                    </div>
                </div>

                <div class="flip-card mx-auto h-56 w-72 cursor-pointer" id="homeFlashcard" data-reveal>
                    <div class="flip-card-inner h-full w-full">
                        <div class="flip-face flex h-full flex-col items-center justify-center rounded-2xl border border-line bg-armor2 p-6 text-center shadow-softLg">
                            <p class="text-xs font-bold uppercase tracking-widest text-ink/40">{{ __('site.home.flip_word') }}</p>
                            <p class="mt-3 font-display text-[25px] font-semibold text-ink">wanderlust</p>
                            <p class="mt-1 font-mono text-[13px] text-sky">/ˈwɒndəlʌst/</p>
                            <p class="mt-5 text-[12px] text-ink/40">{{ __('site.home.flip_hint') }}</p>
                        </div>
                        <div class="flip-face flip-back flex h-full flex-col items-center justify-center rounded-2xl bg-navy p-6 text-center text-white shadow-softLg">
                            <p class="text-xs font-bold uppercase tracking-widest text-gold">{{ __('site.home.flip_translation') }}</p>
                            <p class="mt-3 font-display text-[23px] font-semibold">{{ __('site.glossary.wanderlust') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ===================== PHIL ===================== --}}
    <section class="bg-armor2 py-20 sm:py-24">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div data-reveal class="grid items-center gap-10 rounded-2xl border border-line bg-armor p-8 shadow-soft sm:p-12 lg:grid-cols-2">
                <div>
                    <span class="inline-flex items-center gap-2 rounded-full bg-brand/10 px-3.5 py-1.5 text-xs font-bold uppercase tracking-wider text-brand">{{ __('site.home.phil_badge') }}</span>
                    <h2 class="mt-4 font-display text-[26px] font-semibold text-ink sm:text-[32px]">{{ __("site.home.phil_title_1") }} <span class="text-brand">Phil</span></h2>
                    <p class="mt-3 max-w-md text-[15.5px] leading-relaxed text-ink/60">{{ __('site.home.phil_text') }}</p>
                </div>
                <div class="space-y-3 rounded-xl bg-armor2 p-5">
                    <div class="flex items-end gap-2">
                        <span class="grid h-7 w-7 shrink-0 place-items-center rounded-full bg-navy text-gold">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10" /><path d="M8 14s1.5 2 4 2 4-2 4-2" /></svg>
                        </span>
                        <div class="max-w-[80%] rounded-xl rounded-bl-sm border border-line bg-armor px-3.5 py-2.5 text-[13.5px] leading-relaxed text-ink shadow-sm">В чём разница между «a few» и «few»?</div>
                    </div>
                    <div class="flex justify-end">
                        <div class="max-w-[80%] rounded-xl rounded-br-sm bg-navy px-3.5 py-2.5 text-[13.5px] leading-relaxed text-white shadow-sm">«A few» значит «несколько» — I have a few friends. А «few» значит «почти нет» — так что смысл противоположный.</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ===================== ДОСТИЖЕНИЯ (иллюстративно, тёмно-синяя секция) ===================== --}}
    <section id="achievements" class="bg-navy py-20 sm:py-28">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="mx-auto max-w-2xl text-center" data-reveal>
                <p class="text-xs font-bold uppercase tracking-[0.2em] text-gold">{{ __('site.home.achievements_eyebrow') }}</p>
                <h2 class="mt-3 font-display text-[32px] font-semibold text-white sm:text-[38px]">{{ __('site.home.achievements_title') }}</h2>
                <div class="mx-auto mt-4 h-[3px] w-14 bg-gradient-to-r from-[#C9A961] to-[#D4AF37]"></div>
                <p class="mt-4 text-[16px] text-white/60">{{ __('site.home.achievements_text') }}</p>
            </div>

            <div class="mt-16 grid grid-cols-2 gap-5 sm:grid-cols-3 lg:grid-cols-5">
                @foreach ([
                    ['label' => 'Первый урок', 'icon' => '<path d="M22 10 12 5 2 10l10 5 10-5Z" /><path d="M6 12v5c0 1.5 3 3 6 3s6-1.5 6-3v-5" />'],
                    ['label' => 'Неделя подряд', 'icon' => '<path d="M12 2c1 3-2 4-2 7a4 4 0 0 0 8 0c0-1-.4-2-1-3 2 1 3 3.5 3 6a7 7 0 1 1-14 0c0-4 2-6 3-7 1-1 2-2 3-3Z" fill="currentColor" stroke="none" />'],
                    ['label' => '100 слов', 'icon' => '<path d="m5 8 6 6" /><path d="m4 14 6-6 2-3" /><path d="M2 5h12" /><path d="m22 22-5-10-5 10" /><path d="M14 18h6" />'],
                    ['label' => 'Тест на 100%', 'icon' => '<path d="m3 17 2 2 4-4" /><path d="M13 6h8M13 12h8M13 18h8" />'],
                    ['label' => 'Уровень A2', 'icon' => '<circle cx="12" cy="8" r="6" /><path d="M15.477 12.89 17 22l-5-3-5 3 1.523-9.11" />'],
                ] as $badge)
                    <div data-reveal class="flex flex-col items-center rounded-2xl border border-white/10 bg-white/[.03] p-6 text-center transition hover:border-gold/40 hover:bg-white/[.05]">
                        <span class="grid h-14 w-14 place-items-center rounded-full bg-gold text-navy">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">{!! $badge['icon'] !!}</svg>
                        </span>
                        <p class="mt-3 text-[13px] font-bold leading-tight text-white">{{ $badge['label'] }}</p>
                    </div>
                @endforeach

                <div data-reveal class="flex flex-col items-center rounded-2xl border border-white/10 bg-white/[.03] p-6 text-center opacity-50">
                    <span class="grid h-14 w-14 place-items-center rounded-full bg-white/10 text-white/50">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="11" width="16" height="10" rx="2" /><path d="M8 11V7a4 4 0 0 1 8 0v4" /></svg>
                    </span>
                    <p class="mt-3 text-[13px] font-bold leading-tight text-white/50">Мастер грамматики</p>
                </div>
            </div>
        </div>
    </section>

    {{-- ===================== CTA (золотой градиент) ===================== --}}
    <section class="bg-gradient-to-br from-[#C9A961] to-[#D4AF37] py-20 text-center sm:py-24">
        <div class="mx-auto max-w-2xl px-4 sm:px-6 lg:px-8" data-reveal>
            <h2 class="font-display text-[28px] font-semibold text-navy sm:text-[36px]">{{ __('site.home.cta_title') }}</h2>
            <p class="mx-auto mt-3 max-w-md text-[15.5px] text-navy/70">{{ __('site.home.cta_text') }}</p>
            <a href="{{ route('register') }}" class="mt-8 inline-flex items-center gap-2 rounded bg-navy px-8 py-3.5 text-xs font-bold uppercase tracking-wider text-white shadow-xl transition hover:-translate-y-0.5">
                {{ __('site.home.cta_button') }}
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 5l7 7-7 7" /></svg>
            </a>
        </div>
    </section>

@endsection

@push('scripts')
    <script>
        var homeFlashcard = document.getElementById('homeFlashcard');
        if (homeFlashcard) {
            homeFlashcard.addEventListener('click', function () {
                this.classList.toggle('is-flipped');
            });
        }
    </script>
@endpush
