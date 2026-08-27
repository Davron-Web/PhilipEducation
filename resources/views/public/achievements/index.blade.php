{{-- Прогресс: календарь активности (по реальным датам завершения уроков)
     + достижения (полученные — золотая медаль, не полученные — серый замок). --}}
@extends('layouts.app')

@section('title', 'Прогресс')
@section('page_title', 'Достижения')
@section('meta_description', 'Ваш прогресс в изучении английского: активность по дням и достижения.')

@section('content')
    <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
        <div class="mb-8" data-reveal>
            <h1 class="text-3xl font-extrabold text-ink sm:text-4xl">Ваш прогресс</h1>
            <p class="mt-1 text-ink/60">Продолжайте — у вас отлично получается!</p>
        </div>

        {{-- ===== Календарь активности ===== --}}
        <x-ui.card :hover="false" class="mb-8 overflow-x-auto" data-reveal>
            <h2 class="mb-4 text-sm font-bold uppercase tracking-wide text-ink/50">Активность за 12 недель</h2>

            @php $cellClasses = ['bg-ink/5', 'bg-sky/25', 'bg-sky/50', 'bg-brand/70', 'bg-ink']; @endphp

            <div class="flex gap-1">
                @foreach ($heatmap['weeks'] as $week)
                    <div class="flex flex-col gap-1">
                        @foreach ($week as $day)
                            @php
                                $ratio = $day['count'] / max(1, $heatmap['max']);
                                $level = match (true) {
                                    $day['count'] === 0 => 0,
                                    $ratio <= 0.25 => 1,
                                    $ratio <= 0.5 => 2,
                                    $ratio <= 0.75 => 3,
                                    default => 4,
                                };
                            @endphp
                            <div
                                class="h-3 w-3 rounded-sm {{ $cellClasses[$level] }}"
                                title="{{ \Illuminate\Support\Carbon::parse($day['date'])->translatedFormat('d M') }} — {{ $day['count'] }} {{ $day['count'] === 1 ? 'урок' : 'уроков' }}"
                            ></div>
                        @endforeach
                    </div>
                @endforeach
            </div>

            <div class="mt-4 flex items-center gap-1.5 text-xs font-medium text-ink/40">
                Меньше
                @foreach ($cellClasses as $cls)
                    <div class="h-3 w-3 rounded-sm {{ $cls }}"></div>
                @endforeach
                Больше
            </div>
        </x-ui.card>

        {{-- ===== Достижения ===== --}}
        <div class="mb-4 flex items-baseline justify-between" data-reveal>
            <h2 class="text-xl font-extrabold text-ink">Достижения</h2>
            <span class="text-sm font-semibold text-ink/50">{{ $earned->count() }} из {{ $achievements->count() }} получено</span>
        </div>

        @if ($achievements->isEmpty())
            <x-ui.card :hover="false" class="py-16 text-center">
                <p class="text-lg font-semibold text-ink">Достижения ещё не настроены</p>
                <p class="mt-1 text-ink/50">Загляните позже.</p>
            </x-ui.card>
        @else
            <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                @foreach ($achievements as $achievement)
                    @php $isEarned = (bool) $earned->get($achievement->id); @endphp
                    <div
                        class="flex flex-col items-center rounded-2xl border border-white/10 bg-armor2/70 p-6 text-center shadow-lg shadow-ink/5 backdrop-blur-xl transition duration-300 ease-out {{ $isEarned ? 'hover:-translate-y-1.5 hover:border-brand/30 hover:shadow-2xl hover:shadow-brand/15' : 'opacity-70' }}"
                        data-reveal
                    >
                        <span
                            class="mb-3 flex h-16 w-16 items-center justify-center rounded-full {{ $isEarned ? 'bg-gradient-to-br from-sun to-amber-500 shadow-lg shadow-sun/40' : 'bg-ink/10' }}"
                        >
                            @if ($isEarned)
                                <svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="#78350F" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M8 21h8M12 17v4M7 4h10v4a5 5 0 0 1-10 0V4Z" /><path d="M7 6H4a1 1 0 0 0-1 1c0 2.5 1.8 4.5 4.2 4.9M17 6h3a1 1 0 0 1 1 1c0 2.5-1.8 4.5-4.2 4.9" /></svg>
                            @else
                                <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-ink/30"><rect x="5" y="11" width="14" height="10" rx="2" /><path d="M8 11V7a4 4 0 0 1 8 0v4" /></svg>
                            @endif
                        </span>

                        <h3 class="text-base font-bold text-ink">{{ $achievement->title }}</h3>
                        <p class="mt-1 text-sm text-ink/50">{{ $achievement->description }}</p>

                        <div class="mt-4 flex flex-col items-center gap-1">
                            <x-ui.badge :variant="$isEarned ? 'accent' : 'neutral'">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor" class="mr-0.5"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" /></svg>
                                {{ $achievement->points }} очков
                            </x-ui.badge>
                            @if ($isEarned && optional($earned->get($achievement->id))->pivot?->earned_at)
                                <span class="text-xs text-ink/40">
                                    Получено {{ \Illuminate\Support\Carbon::parse($earned->get($achievement->id)->pivot->earned_at)->translatedFormat('d M Y') }}
                                </span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
@endsection
