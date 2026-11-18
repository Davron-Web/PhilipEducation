{{-- Гостевая главная страница. Использует общий layout (layouts.app) —
     шапку, подвал и виджет Phil со всего сайта — чтобы дизайн был
     полностью единым, а не отдельной «одноразовой» страницей.
     Структура секций (hero → цифры → возможности списком → Phil →
     шкала CEFR → тарифы → отзывы → финальный CTA) — по макету, который
     утвердил заказчик; палитра и шрифты — родные, тема "Philip Elite"
     (тёмно-синий + золото, антиква в заголовках), без нового бренда. --}}
@extends('layouts.app')

@section('title', 'Philip Education')
@section('page_title', __('site.meta.home_title'))
@section('meta_description', __('site.meta.home_description'))

@push('styles')
    <style>
        /* Тёмный градиент в тон навигации (та же пара --pe-navy/--pe-navy2,
           что и bg-navy шапки) + два расфокусированных пятна для глубины. */
        .hero-mesh {
            background: linear-gradient(135deg, var(--pe-navy), var(--pe-navy2));
        }

        .hero-title {
            font-size: clamp(2.375rem, 5vw, 4.25rem);
            line-height: 1.06;
            letter-spacing: -0.02em;
        }

        /* Заливка акцентной фразы градиентом sky→brand — тот же приём, что и
           градиентная плашка карточек ниже, просто на тексте через
           background-clip. Без фиолетового: это добавило бы в палитру
           оттенок, которого сознательно нет во всей остальной синей теме. */
        .hero-accent {
            background: linear-gradient(90deg, var(--pe-sky), var(--pe-brand));
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }

        .hero-blob {
            position: absolute;
            border-radius: 9999px;
            filter: blur(70px);
            pointer-events: none;
        }
        .hero-blob-a { top: -10%; right: 5%; width: 22rem; height: 22rem; background: rgb(47 168 224 / .22); }
        .hero-blob-b { bottom: -15%; left: 0; width: 20rem; height: 20rem; background: rgb(30 58 138 / .35); }

        /* Стеклянная вторичная кнопка — обычной непрозрачной bg-white/10 не
           хватало бы глубины на градиентном фоне, backdrop-blur держит эффект
           стекла даже поверх пятен выше. */
        .btn-glass {
            background: rgb(255 255 255 / .08);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            border: 1px solid rgb(255 255 255 / .25);
            transition: background-color .2s ease, border-color .2s ease;
        }
        .btn-glass:hover { background: rgb(255 255 255 / .16); border-color: rgb(255 255 255 / .45); }

        @media (prefers-reduced-motion: no-preference) {
            .hero-blob-a { animation: heroFloatA 9s ease-in-out infinite; }
            .hero-blob-b { animation: heroFloatB 11s ease-in-out infinite; }
            @keyframes heroFloatA { 0%, 100% { transform: translate(0, 0); } 50% { transform: translate(-1.25rem, 1.5rem); } }
            @keyframes heroFloatB { 0%, 100% { transform: translate(0, 0); } 50% { transform: translate(1.5rem, -1rem); } }
        }

        .scale-track {
            background: linear-gradient(90deg, var(--pe-skylight), var(--pe-sky) 33%, var(--pe-brand) 66%, var(--pe-navy) 100%);
        }

        @media (prefers-reduced-motion: no-preference) {
            .chat-bubble { opacity: 0; transform: translateY(10px) scale(.98); animation: chatPop .5s ease forwards; }
            .chat-bubble:nth-child(1) { animation-delay: .5s; }
            .chat-bubble:nth-child(2) { animation-delay: 1s; }
            .chat-bubble:nth-child(3) { animation-delay: 1.5s; }
            @keyframes chatPop { to { opacity: 1; transform: translateY(0) scale(1); } }
        }
    </style>
@endpush

@section('content')

    {{-- ===================== HERO ===================== --}}
    <section class="hero-mesh relative overflow-hidden bg-navy">
        <div class="hero-blob hero-blob-a" aria-hidden="true"></div>
        <div class="hero-blob hero-blob-b" aria-hidden="true"></div>

        <div class="relative mx-auto grid max-w-7xl items-center gap-12 px-4 py-20 sm:px-6 lg:grid-cols-2 lg:px-8 lg:py-28">
            <div>
                <p class="text-sm font-bold text-gold" data-reveal style="transition-delay: 0ms">{{ __('site.home.hero_eyebrow') }}</p>

                <h1 class="hero-title mt-5 font-display font-semibold text-white" data-reveal style="transition-delay: 80ms">
                    {{ __('site.home.hero_title_1') }}
                    <span class="hero-accent">{{ __('site.home.hero_title_accent') }}</span>
                </h1>

                <p class="mt-5 max-w-xl text-[17px] leading-relaxed text-white/65" data-reveal style="transition-delay: 160ms">
                    {{ __('site.home.hero_text') }}
                </p>

                <div class="mt-9 flex flex-wrap items-center gap-4" data-reveal style="transition-delay: 240ms">
                    <x-ui.button :href="route('register')" size="lg">
                        {{ __('site.home.cta_start') }}
                    </x-ui.button>
                    <a href="#features" class="btn-glass inline-flex items-center gap-2 rounded px-8 py-3 text-xs font-bold uppercase tracking-wider text-white">
                        {{ __('site.home.cta_levels') }}
                    </a>
                </div>

                <div class="mt-12 flex flex-wrap items-center gap-x-8 gap-y-3 border-t border-white/10 pt-6 text-[13px] text-white/50" data-reveal style="transition-delay: 320ms">
                    <div><span class="font-display text-xl font-semibold text-gold" data-counter data-target="{{ $totalUsers }}">0</span> {{ __('site.home.stat_students') }}</div>
                    <div><span class="font-display text-xl font-semibold text-gold" data-counter data-target="{{ $totalWords }}">0</span> {{ __('site.home.stat_words') }}</div>
                    <div><span class="font-display text-xl font-semibold text-gold" data-counter data-target="{{ $totalLessons }}">0</span> {{ __('site.home.stat_lessons') }}</div>
                </div>
            </div>

            {{-- Витрина «разговор с Phil» — иллюстративный пример, показывает
                 главную фичу ассистента (разбор ошибок в реальном времени). --}}
            <div class="relative" data-reveal style="transition-delay: 120ms">
                <div class="overflow-hidden rounded-2xl border border-line bg-armor2 p-6 shadow-[0_35px_70px_-20px_rgb(0_0_0/.55)] sm:p-7">
                    <div class="mb-5 flex items-center gap-3 border-b border-line pb-5">
                        <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full text-brand">
                            <x-mascot :size="24" />
                        </span>
                        <div>
                            <p class="text-sm font-bold text-ink">Phil</p>
                            <p class="text-xs text-ink/40">{{ __('site.home.hero_chat_role') }}</p>
                        </div>
                    </div>
                    <div class="space-y-3">
                        <div class="chat-bubble max-w-[85%] rounded-2xl rounded-tl-sm bg-armor px-4 py-3 text-[15px] text-ink">
                            Hi! What did you do this weekend?
                        </div>
                        <div class="chat-bubble ml-auto max-w-[85%] rounded-2xl rounded-tr-sm bg-brand px-4 py-3 text-[15px] font-medium text-white">
                            I go to my grandmother village
                        </div>
                        <div class="chat-bubble max-w-[90%] rounded-2xl rounded-tl-sm bg-armor px-4 py-3 text-[15px] text-ink">
                            Almost! Say "I went" — past simple. Try the sentence again?
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ===================== СТАТИСТИКА ===================== --}}
    <section class="border-y border-line bg-armor2">
        <div class="mx-auto grid max-w-7xl grid-cols-2 gap-8 px-4 py-8 sm:px-6 sm:grid-cols-4 lg:px-8">
            <div>
                <p class="font-display text-3xl font-bold text-ink" data-counter data-target="{{ $totalUsers }}">0</p>
                <p class="mt-1 text-sm text-ink/60">{{ __('site.home.stat_students') }}</p>
            </div>
            <div>
                <p class="font-display text-3xl font-bold text-ink" data-counter data-target="{{ $totalWords }}">0</p>
                <p class="mt-1 text-sm text-ink/60">{{ __('site.home.stat_words') }}</p>
            </div>
            <div>
                <p class="font-display text-3xl font-bold text-ink" data-counter data-target="{{ $totalLessons }}">0</p>
                <p class="mt-1 text-sm text-ink/60">{{ __('site.home.stat_lessons') }}</p>
            </div>
            <div>
                <p class="font-display text-3xl font-bold text-ink" data-counter data-target="{{ $levels->count() }}">0</p>
                <p class="mt-1 text-sm text-ink/60">{{ __('site.home.stat_levels') }}</p>
            </div>
        </div>
    </section>

    {{-- ===================== ВОЗМОЖНОСТИ (редакторский список, не сетка карточек) ===================== --}}
    @php
        $sections = [
            ['label' => __('site.nav.lessons'), 'desc' => __('site.home.sections.lessons'), 'route' => 'lessons.index'],
            ['label' => __('site.nav.grammar'), 'desc' => __('site.home.sections.grammar'), 'route' => 'grammartopics.index'],
            ['label' => __('site.nav.vocabulary'), 'desc' => __('site.home.sections.vocabulary'), 'route' => 'words.index'],
            ['label' => __('site.nav.expressions'), 'desc' => __('site.home.sections.expressions'), 'route' => 'expressions.index'],
            ['label' => __('site.nav.exercises'), 'desc' => __('site.home.sections.exercises'), 'route' => 'exercises.index'],
            ['label' => __('site.nav.tests'), 'desc' => __('site.home.sections.tests'), 'route' => 'tests.index'],
            ['label' => __('site.nav.ielts'), 'desc' => __('site.home.sections.ielts'), 'route' => 'ielts.index'],
            ['label' => __('site.nav.books'), 'desc' => __('site.home.sections.books'), 'route' => 'books.index'],
            ['label' => __('site.nav.achievements'), 'desc' => __('site.home.sections.achievements'), 'route' => 'achievements.index'],
        ];
    @endphp

    <section id="features" class="bg-armor py-20 sm:py-28">
        <div class="mx-auto grid max-w-7xl gap-14 px-4 sm:px-6 lg:grid-cols-[minmax(0,20rem)_1fr] lg:px-8">
            <div data-reveal>
                <p class="text-xs font-bold uppercase tracking-[0.2em] text-brand">{{ __('site.home.why_eyebrow') }}</p>
                <h2 class="mt-3 font-display text-[32px] font-semibold leading-tight text-ink">{{ __('site.home.why_title') }}</h2>
                <div class="mt-4 h-[3px] w-14 bg-gradient-to-r from-[var(--pe-brand)] to-[var(--pe-sky)]"></div>
                <p class="mt-5 text-[15.5px] leading-relaxed text-ink/60">{{ __('site.home.why_text') }}</p>
            </div>

            <div class="divide-y divide-line border-t border-line" data-reveal>
                @foreach ($sections as $s)
                    <a href="{{ route($s['route']) }}" class="group grid gap-2 py-6 transition hover:bg-armor2/60 sm:grid-cols-[10rem_1fr] sm:gap-8 sm:px-3">
                        <h3 class="font-display font-semibold text-ink group-hover:text-brand">{{ $s['label'] }}</h3>
                        <p class="text-[14.5px] leading-relaxed text-ink/60">{{ $s['desc'] }}</p>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ===================== PHIL (тёмная секция, один яркий акцент) ===================== --}}
    <section id="phil" class="bg-navy py-20 sm:py-24">
        <div class="mx-auto grid max-w-7xl items-center gap-14 px-4 sm:px-6 lg:grid-cols-2 lg:px-8">
            <div data-reveal>
                <p class="text-sm font-bold text-gold">{{ __('site.home.phil_badge') }}</p>
                <h2 class="mt-4 font-display text-[28px] font-semibold leading-tight text-white sm:text-[34px]">
                    {{ __('site.home.phil_title_1') }} <span class="text-gold">{{ __('site.home.phil_title_2') }}</span>
                </h2>
                <p class="mt-5 max-w-md text-[15.5px] leading-relaxed text-white/65">{{ __('site.home.phil_text') }}</p>
                <button type="button" onclick="window.openPhilWidget && window.openPhilWidget()" class="mt-8 inline-flex items-center gap-2 rounded bg-white px-6 py-3 text-xs font-bold uppercase tracking-wider text-navy transition hover:-translate-y-0.5 hover:bg-gold">
                    {{ __('site.home.phil_cta') }}
                </button>
            </div>

            <div class="rounded-2xl border border-white/10 bg-white/5 p-6 sm:p-7" data-reveal>
                <p class="mb-4 text-xs font-bold uppercase tracking-wider text-white/40">{{ __('site.home.phil_demo_label') }}</p>
                <div class="space-y-3">
                    <div class="max-w-[90%] rounded-2xl rounded-tl-sm bg-white/10 px-4 py-3 text-[15px] text-white">
                        {{ __('site.home.phil_demo_question') }}
                    </div>
                    <div class="ml-auto max-w-[90%] rounded-2xl rounded-tr-sm bg-gold px-4 py-3 text-[15px] font-medium text-navy">
                        {{ __('site.home.phil_demo_answer') }}
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ===================== ШКАЛА CEFR ===================== --}}
    <section id="level" class="bg-armor2 py-20 sm:py-28">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="grid items-start gap-14 lg:grid-cols-2">
                <div data-reveal>
                    <p class="text-xs font-bold uppercase tracking-[0.2em] text-brand">{{ __('site.home.levels_eyebrow') }}</p>
                    <h2 class="mt-3 font-display text-[32px] font-semibold leading-tight text-ink">{{ __('site.home.levels_title') }}</h2>
                    <p class="mt-5 max-w-md text-[15.5px] leading-relaxed text-ink/60">{{ __('site.home.levels_text') }}</p>
                    <x-ui.button :href="route('register')" class="mt-7">{{ __('site.home.levels_cta') }}</x-ui.button>
                    <p class="mt-4 text-[13px] text-ink/45">{{ __('site.home.levels_note') }}</p>
                </div>

                <div data-reveal>
                    <div class="h-2.5 rounded-full scale-track"></div>
                    <div class="mt-4 grid grid-cols-6 text-center">
                        @foreach ($levels as $level)
                            <div>
                                <p class="font-display font-bold text-ink">{{ $level['code'] }}</p>
                                <p class="mt-1 text-xs text-ink/50">{{ __('site.home.level_labels.'.strtolower($level['code'])) }}</p>
                                <p class="mt-1 text-[11px] text-ink/35">{{ __('site.home.lessons_count', ['count' => $level['lessons_count']]) }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ===================== ТАРИФЫ (реальные планы из БД) ===================== --}}
    <section id="pricing" class="border-y border-line bg-armor">
        <div class="mx-auto max-w-7xl px-4 py-20 sm:px-6 sm:py-28 lg:px-8">
            <div class="max-w-xl" data-reveal>
                <p class="text-xs font-bold uppercase tracking-[0.2em] text-brand">{{ __('site.home.pricing_eyebrow') }}</p>
                <h2 class="mt-3 font-display text-[32px] font-semibold leading-tight text-ink">{{ __('site.home.pricing_title') }}</h2>
            </div>

            <div class="mt-12 grid gap-6 lg:grid-cols-4">
                {{-- Бесплатный уровень --}}
                <div class="rounded-2xl border border-line bg-armor2 p-8" data-reveal>
                    <h3 class="font-display text-lg font-bold text-ink">{{ __('site.billing.free_title') }}</h3>
                    <p class="mt-2 text-sm text-ink/60">{{ __('site.billing.free_text') }}</p>
                    <p class="mt-6 font-display text-3xl font-bold text-ink">0 <span class="text-base font-medium text-ink/50">TJS</span></p>
                    <ul class="mt-6 space-y-3 text-sm text-ink/70">
                        <li class="flex gap-2"><span class="text-brand">✓</span> {{ __('site.home.pricing_free_feature_1') }}</li>
                        <li class="flex gap-2"><span class="text-brand">✓</span> {{ __('site.home.pricing_free_feature_2') }}</li>
                        <li class="flex gap-2"><span class="text-brand">✓</span> {{ __('site.home.pricing_free_feature_3') }}</li>
                        <li class="flex gap-2"><span class="text-brand">✓</span> {{ __('site.home.pricing_free_feature_4') }}</li>
                    </ul>
                    <x-ui.button :href="route('register')" variant="outline" class="mt-8 w-full">{{ __('site.home.cta_start') }}</x-ui.button>
                </div>

                {{-- Три платных плана — те же самые, в том же порядке, что и на
                     /plans (Billing\SubscriptionController::plans()), а не один
                     "выгодный" план: иначе эта карточка расходилась бы
                     с реальным набором тарифов. --}}
                @foreach ($plans as $plan)
                    @php $isYearly = $plan->code === 'yearly'; @endphp
                    <div
                        class="relative rounded-2xl p-8 {{ $isYearly ? 'bg-navy text-white' : 'border border-line bg-armor2 text-ink' }}"
                        data-reveal
                    >
                        @if ($isYearly)
                            <span class="absolute right-8 top-8 text-xs font-bold uppercase tracking-wider text-gold">{{ __('site.home.levels_popular') }}</span>
                        @endif
                        <h3 class="font-display text-lg font-bold">{{ $plan->name }}</h3>
                        <p class="mt-2 text-sm {{ $isYearly ? 'text-white/60' : 'text-ink/60' }}">{{ __('site.billing.subtitle') }}</p>
                        <p class="mt-6 font-display text-3xl font-bold">
                            {{ number_format($plan->price, 2, '.', ' ') }}
                            <span class="text-base font-medium {{ $isYearly ? 'text-white/60' : 'text-ink/50' }}">{{ $plan->currency }}</span>
                        </p>
                        <p class="mt-1 text-[13px] {{ $isYearly ? 'text-white/45' : 'text-ink/45' }}">
                            {{ __('site.billing.per_day', ['price' => number_format($plan->price_per_day, 2, '.', ' ')]) }}
                        </p>
                        <ul class="mt-6 space-y-3 text-sm {{ $isYearly ? 'text-white/80' : 'text-ink/70' }}">
                            <li class="flex gap-2"><span class="{{ $isYearly ? 'text-gold' : 'text-brand' }}">✓</span> {{ __('site.home.pricing_pro_feature_1') }}</li>
                            <li class="flex gap-2"><span class="{{ $isYearly ? 'text-gold' : 'text-brand' }}">✓</span> {{ __('site.home.pricing_pro_feature_2') }}</li>
                            <li class="flex gap-2"><span class="{{ $isYearly ? 'text-gold' : 'text-brand' }}">✓</span> {{ __('site.home.pricing_pro_feature_3') }}</li>
                            <li class="flex gap-2"><span class="{{ $isYearly ? 'text-gold' : 'text-brand' }}">✓</span> {{ __('site.home.pricing_pro_feature_4') }}</li>
                        </ul>
                        <x-ui.button :href="route('login')" :variant="$isYearly ? 'primary' : 'outline'" class="mt-8 w-full">{{ __('site.billing.subscribe') }}</x-ui.button>
                    </div>
                @endforeach
            </div>

            <a href="{{ route('billing.plans') }}" class="mt-8 inline-flex items-center gap-1.5 text-xs font-bold uppercase tracking-wider text-brand hover:underline" data-reveal>
                {{ __('site.home.pricing_view_all') }}
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6"><path d="M5 12h14M13 5l7 7-7 7" /></svg>
            </a>
        </div>
    </section>

    {{-- ===================== ОТЗЫВЫ ===================== --}}
    <section class="bg-armor py-20 sm:py-28">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <p class="text-xs font-bold uppercase tracking-[0.2em] text-brand" data-reveal>{{ __('site.home.testimonials_eyebrow') }}</p>
            <div class="mt-8 grid gap-10 md:grid-cols-3">
                @foreach (__('site.home.testimonials') as $t)
                    <blockquote data-reveal>
                        <p class="font-display text-lg leading-snug text-ink">&laquo;{{ $t['quote'] }}&raquo;</p>
                        <footer class="mt-4 text-sm text-ink/50">{{ $t['name'] }} &middot; {{ $t['level'] }}</footer>
                    </blockquote>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ===================== CTA (синий градиент) ===================== --}}
    <section class="bg-gradient-to-br from-[var(--pe-brand)] to-[var(--pe-sky)] py-20 text-center sm:py-24">
        <div class="mx-auto max-w-2xl px-4 sm:px-6 lg:px-8" data-reveal>
            <h2 class="font-display text-[28px] font-semibold text-white sm:text-[36px]">{{ __('site.home.cta_title') }}</h2>
            <p class="mx-auto mt-3 max-w-md text-[15.5px] text-white/70">{{ __('site.home.cta_text') }}</p>
            <a href="{{ route('register') }}" class="mt-8 inline-flex items-center gap-2 rounded bg-navy px-8 py-3.5 text-xs font-bold uppercase tracking-wider text-white shadow-xl transition hover:-translate-y-0.5">
                {{ __('site.home.cta_button') }}
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 5l7 7-7 7" /></svg>
            </a>
        </div>
    </section>

@endsection
