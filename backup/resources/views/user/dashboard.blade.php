{{-- Личный кабинет: приветствие, прогресс уровня, урок «продолжить»,
     streak, статистика и задание дня. --}}
@extends('layouts.app')

@section('title', 'Личный кабинет')
@section('meta_description', 'Личный кабинет ученика Philip Education — прогресс, streak и задание дня.')

@section('content')
    <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">

        {{-- Голосовое приветствие Phil --}}
        <x-voice-greeting :stats="$stats" :next-lesson="$nextLesson" />

        {{-- Приветствие --}}
        <div class="mb-8 flex flex-wrap items-end justify-between gap-4" data-reveal>
            <div>
                <h1 class="text-3xl font-extrabold text-ink sm:text-4xl">
                    С возвращением, {{ explode(' ', auth()->user()->name)[0] }}!
                </h1>
                <p class="mt-1 text-ink/60">Продолжим заниматься английским — вот что у вас сейчас.</p>
            </div>

            <div class="flex flex-wrap items-stretch gap-3">

            {{-- Опыт и звание --}}
            <div class="rounded-2xl border border-brand/30 bg-brand/10 px-4 py-3 text-brand">
                <div class="flex items-center gap-2">
                    <span class="text-xl leading-none">{{ $title?->icon ?? '🌱' }}</span>
                    <div class="leading-tight">
                        <p class="text-lg font-extrabold" x-data x-init="countUp($el, 0, {{ $stats['xp'] }}, 800)">0</p>
                        <p class="text-xs font-semibold uppercase tracking-wide text-brand/70">
                            XP · {{ $title?->name ?? 'Новичок' }}
                        </p>
                    </div>
                </div>

                @if ($nextTitle)
                    @php
                        $from = $title?->min_xp ?? 0;
                        $span = max(1, $nextTitle->min_xp - $from);
                        $done = min(100, round((($stats['xp'] - $from) / $span) * 100));
                    @endphp
                    <div class="mt-2 w-40">
                        <div class="h-1.5 overflow-hidden rounded-full bg-brand/20">
                            <div class="h-full rounded-full bg-brand" style="width: {{ $done }}%"></div>
                        </div>
                        <p class="mt-1 text-[11px] font-semibold text-brand/70">
                            До «{{ $nextTitle->name }}» — {{ number_format($nextTitle->min_xp - $stats['xp'], 0, ',', ' ') }} XP
                        </p>
                    </div>
                @else
                    <p class="mt-2 text-[11px] font-semibold text-brand/70">Высшее звание получено</p>
                @endif
            </div>

            {{-- Streak --}}
            <div class="rounded-2xl border border-sun/30 bg-sun/10 px-4 py-3 text-sun">
                <div class="flex items-center gap-2">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 2c1 3-2 4-2 7a4 4 0 0 0 8 0c0-1-.4-2-1-3 2 1 3 3.5 3 6a7 7 0 1 1-14 0c0-4 2-6 3-7 1-1 2-2 3-3Z" /></svg>
                    <div class="leading-tight">
                        <p class="text-lg font-extrabold" x-data x-init="countUp($el, 0, {{ $stats['streak'] }}, 800)">0</p>
                        <p class="text-xs font-semibold uppercase tracking-wide text-sun/70">
                            {{ $stats['streak'] === 1 ? 'день подряд' : 'дней подряд' }}
                        </p>
                    </div>
                </div>
                <div class="mt-2 flex items-center gap-1">
                    @foreach ($streakDays as $day)
                        <div class="flex flex-col items-center gap-1">
                            <span
                                class="h-2.5 w-2.5 rounded-full {{ $day['active'] ? 'bg-sun' : 'bg-sun/15' }} {{ $day['isToday'] ? 'ring-2 ring-sun ring-offset-1 ring-offset-armor' : '' }}"
                                title="{{ $day['label'] }}"
                            ></span>
                        </div>
                    @endforeach
                </div>
            </div>

            </div>
        </div>

        <div class="grid gap-6 lg:grid-cols-3">
            <div class="space-y-6 lg:col-span-2">

                {{-- Прогресс уровня --}}
                <x-ui.card class="!p-6">
                    <div class="flex items-center gap-5">
                        <div class="relative flex h-20 w-20 shrink-0 items-center justify-center">
                            <svg class="h-20 w-20 -rotate-90" viewBox="0 0 100 100">
                                <circle cx="50" cy="50" r="42" fill="none" stroke-width="10" class="stroke-white/10" />
                                <circle
                                    cx="50" cy="50" r="42" fill="none" stroke-width="10" stroke-linecap="round"
                                    stroke="url(#level-ring-gradient)"
                                    stroke-dasharray="264"
                                    style="stroke-dashoffset: 264; transition: stroke-dashoffset 1s ease-out"
                                    x-data
                                    x-init="requestAnimationFrame(() => $el.style.strokeDashoffset = 264 - (264 * {{ $stats['lessons_progress'] }} / 100))"
                                />
                                <defs>
                                    <linearGradient id="level-ring-gradient" x1="0" y1="0" x2="1" y2="1">
                                        <stop offset="0%" stop-color="#7C3AED" />
                                        <stop offset="100%" stop-color="#22D3EE" />
                                    </linearGradient>
                                </defs>
                            </svg>
                            <span class="absolute text-lg font-extrabold text-ink">{{ $stats['lessons_progress'] }}%</span>
                        </div>

                        <div class="min-w-0 flex-1">
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="text-sm font-bold uppercase tracking-wide text-ink/50">Текущий уровень</span>
                                <x-ui.badge variant="level" :level="auth()->user()->level" />
                            </div>
                            {{-- CTA показывается всегда, а не только пока уровень пуст —
                                 уровень определяется один раз, но переопределить его
                                 (прогресс, смена программы) можно в любой момент. --}}
                            @if (auth()->user()->level_id)
                                <p class="mt-1 text-sm text-ink/50">
                                    {{ $stats['lessons_completed'] }} уроков пройдено на пути к следующему уровню.
                                    Ваш уровень: {{ auth()->user()->level->code }} ·
                                    <a href="{{ route('tests.placement.intro') }}" class="font-bold text-brand hover:underline">Пройти заново →</a>
                                </p>
                            @else
                                {{-- Уровень ещё не определён — у 1111 из 1112 студентов
                                     на момент внедрения он был пуст: ни один механизм
                                     раньше его не проставлял. --}}
                                <p class="mt-1 text-sm text-ink/50">
                                    Пройдите тест из 50 вопросов — узнаете точный уровень,
                                    и по нему подберутся уроки.
                                    <a href="{{ route('tests.placement.intro') }}" class="font-bold text-brand hover:underline">Определить уровень →</a>
                                </p>
                            @endif
                        </div>
                    </div>
                </x-ui.card>

                {{-- Продолжить урок --}}
                <div class="relative overflow-hidden rounded-2xl bg-brand p-8 text-white shadow-softLg" data-reveal>
                    <div class="relative">
                        <p class="mb-2 inline-flex items-center gap-1.5 rounded-full bg-white/15 px-3 py-1 text-xs font-bold uppercase tracking-wide">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polygon points="5 3 19 12 5 21 5 3" /></svg>
                            Продолжить обучение
                        </p>

                        @if ($nextLesson)
                            <h2 class="text-2xl font-extrabold sm:text-3xl">{{ $nextLesson->title }}</h2>
                            <p class="mt-2 max-w-md text-white/75">
                                Следующий урок на вашем пути — уровень {{ optional($nextLesson->level)->code ?? '—' }}.
                            </p>
                            <x-ui.button href="{{ route('lessons.show', $nextLesson->id) }}" variant="accent" size="lg" class="mt-6">
                                Продолжить урок
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M13 6l6 6-6 6" /></svg>
                            </x-ui.button>
                        @else
                            <h2 class="text-2xl font-extrabold sm:text-3xl">Все доступные уроки пройдены!</h2>
                            <p class="mt-2 max-w-md text-white/75">Загляните в словарь или тесты, чтобы закрепить знания.</p>
                            <x-ui.button href="{{ route('words.index') }}" variant="accent" size="lg" class="mt-6">Повторить словарь</x-ui.button>
                        @endif
                    </div>
                </div>

                @php
                    // Tailwind не умеет накладывать альфа-канал на цвета,
                    // заданные через var() (--pe-brand и т.д. в
                    // tailwind.config.js) — bg-brand/10, text-ink/60 и любой
                    // другой Классов/NN на этих токенах компилируется в
                    // сплошной непрозрачный цвет, без затемнения. Проверено
                    // напрямую на собранном CSS: bg-brand/10 и даже
                    // повсеместный по сайту text-ink/60 отсутствуют в
                    // выводе вовсе. Это не локальная опечатка, а общее
                    // ограничение системы цвета проекта — чинить его везде
                    // отдельная большая задача, а здесь достаточно
                    // color-mix() в нативном CSS: он подставляет уже
                    // посчитанное браузером значение переменной, минуя
                    // сборщик Tailwind целиком.
                    $accent = [
                        'brand' => '--pe-brand',
                        'sky' => '--pe-sky',
                        'skylight' => '--pe-skylight',
                        'sun' => '--pe-sun',
                        'gold' => '--pe-gold',
                    ];
                @endphp
                <style>
                    .acc-tint { background-color: color-mix(in srgb, var(--accent) 6%, transparent); transition: background-color .2s ease; }
                    .acc-tint:hover { background-color: color-mix(in srgb, var(--accent) 12%, transparent); }
                    .acc-icon { background-color: color-mix(in srgb, var(--accent) 16%, transparent); color: var(--accent); }
                    .acc-ring-bg { stroke: color-mix(in srgb, var(--accent) 16%, transparent); }
                    .acc-ring-fg { stroke: var(--accent); }
                    .acc-lock-bg { background-color: color-mix(in srgb, var(--pe-ink) 10%, transparent); }
                    .acc-lock-icon { color: color-mix(in srgb, var(--pe-ink) 30%, transparent); }
                    .acc-bar-track { background-color: color-mix(in srgb, var(--pe-ink) 10%, transparent); }
                </style>

                {{-- Статистика: у каждой плитки свой акцент и своё кольцо
                     прогресса (кроме часов — для них нет естественной цели,
                     рисовать фальшивый процент не стали). --}}
                <div class="grid grid-cols-2 gap-4 sm:grid-cols-4">
                    @php
                        $tiles = [
                            ['label' => 'Слов выучено', 'value' => $stats['words_learned'], 'icon' => 'M4 19.5A2.5 2.5 0 0 1 6.5 17H20M4 19.5A2.5 2.5 0 0 0 6.5 22H20V4H6.5A2.5 2.5 0 0 0 4 6.5v13Z', 'accent' => 'brand', 'progress' => $stats['words_progress']],
                            ['label' => 'Уроков пройдено', 'value' => $stats['lessons_completed'], 'icon' => 'M12 20h9M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4Z', 'accent' => 'sky', 'progress' => $stats['lessons_progress']],
                            ['label' => 'Часов занятий', 'value' => $stats['hours_studied'], 'icon' => 'M12 8v4l3 3M12 21a9 9 0 1 0 0-18 9 9 0 0 0 0 18Z', 'accent' => 'sun', 'progress' => null],
                            ['label' => 'Достижения', 'value' => $stats['achievements_count'].' / '.$stats['achievements_total'], 'icon' => 'M8 21h8M12 17v4M7 4h10v5a5 5 0 0 1-10 0V4Z', 'accent' => 'gold', 'progress' => $stats['achievements_total'] ? round($stats['achievements_count'] / $stats['achievements_total'] * 100) : 0],
                        ];
                    @endphp
                    @foreach ($tiles as $i => $tile)
                        <x-ui.card class="!p-5 text-center" :hover="false" style="--accent: var({{ $accent[$tile['accent']] }})">
                            <div class="relative mx-auto mb-3 flex h-16 w-16 items-center justify-center">
                                @if ($tile['progress'] !== null)
                                    <svg class="absolute inset-0 h-16 w-16 -rotate-90" viewBox="0 0 64 64">
                                        <circle cx="32" cy="32" r="27" fill="none" stroke-width="5" class="acc-ring-bg" />
                                        <circle
                                            cx="32" cy="32" r="27" fill="none" stroke-width="5" stroke-linecap="round"
                                            class="acc-ring-fg"
                                            stroke-dasharray="170"
                                            style="stroke-dashoffset: 170; transition: stroke-dashoffset 1s ease-out"
                                            x-data x-init="requestAnimationFrame(() => $el.style.strokeDashoffset = 170 - (170 * {{ $tile['progress'] }} / 100))"
                                        />
                                    </svg>
                                @endif
                                <span class="acc-icon relative flex h-11 w-11 items-center justify-center rounded-full">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="{{ $tile['icon'] }}" /></svg>
                                </span>
                            </div>
                            {{-- countUp пишет в textContent через Math.round —
                                 для "4 / 8" (не число) затёр бы текст нулём,
                                 а для 5.2 часа округлил бы до 5. Анимируем
                                 только целые числа, остальное — как есть. --}}
                            @if (is_numeric($tile['value']) && (int) $tile['value'] == $tile['value'])
                                <p class="text-3xl font-extrabold text-ink" x-data x-init="countUp($el, 0, {{ $tile['value'] }}, 800)">0</p>
                            @else
                                <p class="text-3xl font-extrabold text-ink">{{ $tile['value'] }}</p>
                            @endif
                            <p class="mt-0.5 text-xs font-semibold text-ink/50">{{ $tile['label'] }}</p>
                        </x-ui.card>
                    @endforeach
                </div>

                {{-- Быстрый доступ: у каждого раздела свой цвет — без этого
                     все восемь карточек сливались в одинаковый бежевый ряд. --}}
                <x-ui.card class="!p-6" :hover="false">
                    <h3 class="mb-4 text-sm font-bold uppercase tracking-wide text-ink/50">Разделы</h3>
                    @php
                        $sections = [
                            ['label' => 'Уроки', 'route' => 'lessons.index', 'accent' => 'brand', 'icon' => 'M8 6h13M8 12h13M8 18h13M3 6h.01M3 12h.01M3 18h.01'],
                            ['label' => 'Грамматика', 'route' => 'grammartopics.index', 'accent' => 'sky', 'icon' => 'M5 8h9M9 4v4M12 8c-1 3.5-3.5 6-7 8M6 12c1.5 1.5 4 2.5 6 3M14 20l4-9 4 9M15.5 17h5'],
                            ['label' => 'Словарь', 'route' => 'words.index', 'accent' => 'gold', 'icon' => 'M4 19.5A2.5 2.5 0 0 1 6.5 17H20M4 19.5A2.5 2.5 0 0 0 6.5 22H20V4H6.5A2.5 2.5 0 0 0 4 6.5v13Z'],
                            ['label' => 'Выражения', 'route' => 'expressions.index', 'accent' => 'sun', 'icon' => 'M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v10Z'],
                            ['label' => 'Упражнения', 'route' => 'exercises.index', 'accent' => 'skylight', 'icon' => 'M9 11l3 3L22 4M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11'],
                            ['label' => 'Тесты', 'route' => 'tests.index', 'accent' => 'brand', 'icon' => 'M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2M9 5a2 2 0 0 0 2 2h2a2 2 0 0 0 2-2M9 5a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2M9 14l2 2 4-4'],
                            ['label' => 'IELTS', 'route' => 'ielts.index', 'accent' => 'sun', 'icon' => 'M12 2a10 10 0 1 0 0 20 10 10 0 0 0 0-20ZM2 12h20M12 2a15 15 0 0 1 4 10 15 15 0 0 1-4 10 15 15 0 0 1-4-10 15 15 0 0 1 4-10Z'],
                            ['label' => 'Книги', 'route' => 'books.index', 'accent' => 'sky', 'icon' => 'M12 6.5C10.5 5 8 4 4 4v14c4 0 6.5 1 8 2.5V6.5ZM12 6.5C13.5 5 16 4 20 4v14c-4 0-6.5 1-8 2.5V6.5Z'],
                        ];
                    @endphp
                    <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">
                        @foreach ($sections as $section)
                            <a
                                href="{{ route($section['route']) }}"
                                class="acc-tint group flex flex-col items-center gap-2 rounded-xl border border-line p-4 text-center transition hover:-translate-y-0.5 hover:shadow-soft"
                                style="--accent: var({{ $accent[$section['accent']] }})"
                            >
                                <span class="acc-icon flex h-11 w-11 items-center justify-center rounded-full">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="{{ $section['icon'] }}" /></svg>
                                </span>
                                <span class="text-xs font-semibold text-ink/70">{{ $section['label'] }}</span>
                            </a>
                        @endforeach
                    </div>
                </x-ui.card>

                {{-- Достижения: витрина из уже полученных и ближайших целей.
                     У locked-достижений с известным условием — честный
                     мини-прогресс (сколько уже есть из скольки нужно), а не
                     голый замок. --}}
                @if ($achievementPreview->isNotEmpty())
                    <x-ui.card class="!p-6" :hover="false">
                        <div class="mb-4 flex items-center justify-between">
                            <h3 class="text-sm font-bold uppercase tracking-wide text-ink/50">Достижения</h3>
                            <a href="{{ route('achievements.index') }}" class="text-xs font-bold uppercase tracking-wide text-brand hover:underline">Все →</a>
                        </div>
                        <div class="grid grid-cols-3 gap-4 sm:grid-cols-6">
                            @foreach ($achievementPreview as $achievement)
                                <a href="{{ route('achievements.index') }}" class="group flex flex-col items-center gap-1.5 text-center" title="{{ $achievement->title }}">
                                    <span class="flex h-14 w-14 items-center justify-center rounded-full {{ $achievement->is_earned ? 'bg-gradient-to-br from-sun to-amber-500 shadow-lg shadow-sun/40' : 'acc-lock-bg' }}">
                                        @if ($achievement->is_earned)
                                            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="#78350F" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M8 21h8M12 17v4M7 4h10v4a5 5 0 0 1-10 0V4Z" /><path d="M7 6H4a1 1 0 0 0-1 1c0 2.5 1.8 4.5 4.2 4.9M17 6h3a1 1 0 0 1 1 1c0 2.5-1.8 4.5-4.2 4.9" /></svg>
                                        @else
                                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="acc-lock-icon"><rect x="5" y="11" width="14" height="10" rx="2" /><path d="M8 11V7a4 4 0 0 1 8 0v4" /></svg>
                                        @endif
                                    </span>

                                    @if (! $achievement->is_earned && $achievement->progress_percent !== null)
                                        <span class="acc-bar-track block h-1 w-12 overflow-hidden rounded-full">
                                            <span class="block h-full rounded-full bg-brand" style="width: {{ $achievement->progress_percent }}%"></span>
                                        </span>
                                        <span class="text-[10px] font-semibold" style="color: color-mix(in srgb, var(--pe-ink) 40%, transparent)">{{ $achievement->progress_current }}/{{ $achievement->condition_value }}</span>
                                    @endif

                                    <span class="line-clamp-2 text-[11px] font-semibold leading-tight text-ink/60 group-hover:text-brand">{{ $achievement->title }}</span>
                                </a>
                            @endforeach
                        </div>
                    </x-ui.card>
                @endif
            </div>

            {{-- Повторение слов --}}
            <div class="space-y-6">
                @if ($wordsDue > 0)
                    <x-ui.card class="!p-6" :hover="false" data-reveal>
                        <div class="mb-3 flex items-center gap-2">
                            <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-brand/10 text-brand">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 4v6h6M23 20v-6h-6" /><path d="M20.49 9A9 9 0 0 0 5.64 5.64L1 10m22 4-4.64 4.36A9 9 0 0 1 3.51 15" /></svg>
                            </span>
                            <h3 class="text-lg font-bold text-ink">Пора повторить</h3>
                        </div>
                        @php
                            $m100 = $wordsDue % 100;
                            $m10 = $wordsDue % 10;
                            $wordForm = ($m100 >= 11 && $m100 <= 14) ? 'слов'
                                : (($m10 === 1) ? 'слово' : (($m10 >= 2 && $m10 <= 4) ? 'слова' : 'слов'));
                        @endphp
                        <p class="text-sm text-ink/60">
                            {{ $wordsDue }} {{ $wordForm }} ждут повторения — это несколько минут,
                            но именно они держат словарь в памяти.
                        </p>
                        <x-ui.button :href="route('words.review')" class="mt-4 w-full">Повторить</x-ui.button>
                    </x-ui.card>
                @endif

                {{-- Задание дня --}}
                <x-ui.card class="!p-6" :hover="false">
                    <div class="mb-4 flex items-center gap-2">
                        <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-sun/15 text-sun">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2v2M12 20v2M4.9 4.9l1.4 1.4M17.7 17.7l1.4 1.4M2 12h2M20 12h2M4.9 19.1l1.4-1.4M17.7 6.3l1.4-1.4" /><circle cx="12" cy="12" r="4" /></svg>
                        </span>
                        <h3 class="text-lg font-bold text-ink">Задание дня</h3>
                    </div>

                    @if ($dailyExercise)
                        <p class="text-sm text-ink/60">{{ $dailyExercise->lesson->title ?? 'Практика' }}</p>
                        <p class="mt-1 text-lg font-bold text-ink">{{ $dailyExercise->title }}</p>
                        <x-ui.badge variant="accent" class="mt-3">{{ $dailyExercise->type_label }}</x-ui.badge>

                        <x-ui.button href="{{ route('exercises.show', $dailyExercise->id) }}" variant="primary" class="mt-5 w-full">
                            Выполнить задание
                        </x-ui.button>
                    @else
                        <p class="text-sm text-ink/60">Вы уже сделали все доступные упражнения на сегодня. Отличная работа!</p>
                    @endif
                </x-ui.card>

                {{-- Последняя активность --}}
                <x-ui.card class="!p-6" :hover="false">
                    <div class="mb-4 flex items-center gap-2">
                        <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-sky/15 text-sky">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 8v4l3 3M12 21a9 9 0 1 0 0-18 9 9 0 0 0 0 18Z" /></svg>
                        </span>
                        <h3 class="text-lg font-bold text-ink">Последняя активность</h3>
                    </div>

                    @if ($recentXp->isNotEmpty())
                        <ul class="space-y-3">
                            @foreach ($recentXp as $event)
                                <li class="flex items-center justify-between gap-3 text-sm">
                                    <span class="text-ink/70">{{ $event->label() }}</span>
                                    <span class="flex shrink-0 items-center gap-2">
                                        <span class="font-bold text-brand">+{{ $event->amount }} XP</span>
                                        <span class="text-xs text-ink/40">{{ $event->created_at->diffForHumans() }}</span>
                                    </span>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-sm text-ink/60">Как только вы пройдёте первый урок или тест — здесь появится история начислений опыта.</p>
                    @endif
                </x-ui.card>
            </div>
        </div>
    </div>
@endsection
