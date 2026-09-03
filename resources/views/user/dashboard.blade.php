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
                            <p class="mt-1 text-sm text-ink/50">{{ $stats['lessons_completed'] }} уроков пройдено на пути к следующему уровню.</p>
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

                {{-- Статистика --}}
                <div class="grid grid-cols-2 gap-4 sm:grid-cols-4">
                    @php
                        $tiles = [
                            ['label' => 'Слов выучено', 'value' => $stats['words_learned'], 'icon' => 'M4 19.5A2.5 2.5 0 0 1 6.5 17H20M4 19.5A2.5 2.5 0 0 0 6.5 22H20V4H6.5A2.5 2.5 0 0 0 4 6.5v13Z'],
                            ['label' => 'Уроков пройдено', 'value' => $stats['lessons_completed'], 'icon' => 'M12 20h9M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4Z'],
                            ['label' => 'Часов занятий', 'value' => $stats['hours_studied'], 'icon' => 'M12 8v4l3 3M12 21a9 9 0 1 0 0-18 9 9 0 0 0 0 18Z'],
                            ['label' => 'Достижения', 'value' => $stats['achievements_count'].' / '.$stats['achievements_total'], 'icon' => 'M8 21h8M12 17v4M7 4h10v5a5 5 0 0 1-10 0V4Z'],
                        ];
                    @endphp
                    @foreach ($tiles as $tile)
                        <x-ui.card class="!p-5 text-center">
                            <span class="mx-auto mb-2 flex h-10 w-10 items-center justify-center rounded-xl bg-brand/10 text-brand">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="{{ $tile['icon'] }}" /></svg>
                            </span>
                            <p class="text-2xl font-extrabold text-ink">{{ $tile['value'] }}</p>
                            <p class="mt-0.5 text-xs font-semibold text-ink/50">{{ $tile['label'] }}</p>
                        </x-ui.card>
                    @endforeach
                </div>
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
            </div>
        </div>
    </div>
@endsection
