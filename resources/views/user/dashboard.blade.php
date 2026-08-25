{{-- Личный кабинет: приветствие, прогресс уровня, урок «продолжить»,
     streak, статистика и задание дня. --}}
@extends('layouts.app')

@section('title', 'Личный кабинет')
@section('meta_description', 'Личный кабинет ученика Philip Education — прогресс, streak и задание дня.')

@section('content')
    <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">

        {{-- Приветствие --}}
        <div class="mb-8 flex flex-wrap items-end justify-between gap-4" data-reveal>
            <div>
                <h1 class="text-3xl font-extrabold text-ink sm:text-4xl">
                    С возвращением, {{ explode(' ', auth()->user()->name)[0] }}! 👋
                </h1>
                <p class="mt-1 text-ink/60">Продолжим заниматься английским — вот что у вас сейчас.</p>
            </div>

            {{-- Streak --}}
            <div class="flex items-center gap-2 rounded-2xl border border-sun/30 bg-sun/10 px-4 py-2.5 text-amber-700">
                <span class="text-2xl" aria-hidden="true">🔥</span>
                <div class="leading-tight">
                    <p class="text-lg font-extrabold" x-data x-init="countUp($el, 0, {{ $stats['streak'] }}, 800)">0</p>
                    <p class="text-xs font-semibold uppercase tracking-wide text-amber-700/70">
                        {{ $stats['streak'] === 1 ? 'день подряд' : 'дней подряд' }}
                    </p>
                </div>
            </div>
        </div>

        <div class="grid gap-6 lg:grid-cols-3">
            <div class="space-y-6 lg:col-span-2">

                {{-- Прогресс уровня --}}
                <x-ui.card class="!p-6">
                    <div class="flex flex-wrap items-center justify-between gap-2">
                        <div class="flex items-center gap-2">
                            <span class="text-sm font-bold uppercase tracking-wide text-ink/50">Текущий уровень</span>
                            <x-ui.badge variant="level" :level="auth()->user()->level" />
                        </div>
                        <span class="text-sm font-bold text-brand">{{ $stats['lessons_progress'] }}%</span>
                    </div>
                    <div class="mt-3 h-3 w-full overflow-hidden rounded-full bg-ink/10">
                        <div
                            class="h-full rounded-full bg-gradient-to-r from-brand to-sky transition-all duration-1000 ease-out"
                            style="width: 0%"
                            x-data
                            x-init="requestAnimationFrame(() => $el.style.width = '{{ $stats['lessons_progress'] }}%')"
                        ></div>
                    </div>
                    <p class="mt-2 text-sm text-ink/50">{{ $stats['lessons_completed'] }} уроков пройдено на пути к следующему уровню.</p>
                </x-ui.card>

                {{-- Продолжить урок --}}
                <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-ink via-brand to-sky p-8 text-white shadow-2xl shadow-brand/25" data-reveal>
                    <div class="absolute -right-10 -top-10 h-48 w-48 rounded-full bg-white/10 blur-2xl"></div>
                    <div class="absolute -bottom-16 left-10 h-40 w-40 rounded-full bg-sun/20 blur-2xl"></div>

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
                            <h2 class="text-2xl font-extrabold sm:text-3xl">Все доступные уроки пройдены! 🎉</h2>
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

            {{-- Задание дня --}}
            <div>
                <x-ui.card class="!p-6" :hover="false">
                    <div class="mb-4 flex items-center gap-2">
                        <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-sun/20 text-amber-600">
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
                        <p class="text-sm text-ink/60">Вы уже сделали все доступные упражнения на сегодня. Отличная работа! 🎉</p>
                    @endif
                </x-ui.card>
            </div>
        </div>
    </div>
@endsection
