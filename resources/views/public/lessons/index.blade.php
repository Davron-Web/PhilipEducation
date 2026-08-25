{{-- Каталог уроков: поиск, фильтр по уровню, сетка карточек со статусом
     (пройден / доступен / заблокирован — по порядку прохождения). --}}
@extends('layouts.app')

@section('title', 'Уроки')
@section('page_title', 'Каталог уроков')
@section('meta_description', 'Каталог уроков английского языка от A1 до C1 в Philip Education.')

@section('content')
    <div
        class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8"
        x-data="{ search: '', level: 'all' }"
    >
        <div class="mb-8" data-reveal>
            <h1 class="text-3xl font-extrabold text-ink sm:text-4xl">Каталог уроков</h1>
            <p class="mt-1 text-ink/60">{{ $lessons->count() }} уроков — проходите по порядку, чтобы открывать новые.</p>
        </div>

        @if ($lessons->isEmpty())
            <x-ui.card :hover="false" class="py-16 text-center">
                <p class="text-lg font-semibold text-ink">Уроки пока не опубликованы</p>
                <p class="mt-1 text-ink/50">Загляните позже — мы уже готовим материалы.</p>
            </x-ui.card>
        @else
            @php
                $levels = $lessons->pluck('level.code')->filter()->unique()->values();
            @endphp

            {{-- Поиск и фильтр по уровню --}}
            <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between" data-reveal>
                <div class="relative w-full sm:max-w-xs">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" class="pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-ink/40"><circle cx="11" cy="11" r="7" /><path d="m21 21-4.3-4.3" /></svg>
                    <input
                        type="search"
                        x-model="search"
                        placeholder="Поиск по названию урока..."
                        class="w-full rounded-xl border-2 border-ink/10 bg-white/70 py-2.5 pl-10 pr-4 text-sm text-ink placeholder:text-ink/40 backdrop-blur transition focus:border-brand focus:outline-none focus:ring-4 focus:ring-brand/15"
                    >
                </div>

                <div class="flex flex-wrap gap-2">
                    <button
                        type="button"
                        @click="level = 'all'"
                        :class="level === 'all' ? 'bg-brand text-white shadow-md shadow-brand/25' : 'bg-white/70 text-ink/60 hover:bg-brand/5'"
                        class="rounded-full px-4 py-1.5 text-sm font-bold transition"
                    >Все уровни</button>
                    @foreach ($levels as $code)
                        <button
                            type="button"
                            @click="level = '{{ $code }}'"
                            :class="level === '{{ $code }}' ? 'bg-brand text-white shadow-md shadow-brand/25' : 'bg-white/70 text-ink/60 hover:bg-brand/5'"
                            class="rounded-full px-4 py-1.5 text-sm font-bold transition"
                        >{{ $code }}</button>
                    @endforeach
                </div>
            </div>

            {{-- Сетка уроков --}}
            @php $unlocked = true; @endphp
            <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($lessons as $index => $lesson)
                    @php
                        $percent = (int) ($progressByLesson[$lesson->id] ?? 0);
                        $isCompleted = $percent >= 100;
                        $isLocked = ! $unlocked && ! $isCompleted;
                        // следующий урок открывается только после завершения текущего —
                        // единый последовательный путь по всем уровням
                        $unlocked = $isCompleted;
                        $levelCode = optional($lesson->level)->code ?? '';
                    @endphp

                    <div
                        x-show="(level === 'all' || level === '{{ $levelCode }}') && '{{ Str::lower(addslashes($lesson->title)) }}'.includes(search.toLowerCase())"
                        {{-- data-reveal сочетается с x-show: карточка проявится при скролле,
                             как только фильтр вновь сделает её видимой --}}
                    >
                        @if ($isLocked)
                            <div class="relative overflow-hidden rounded-2xl border border-ink/10 bg-ink/5 p-5 opacity-60" data-reveal>
                                <div class="absolute right-4 top-4 flex h-8 w-8 items-center justify-center rounded-full bg-ink/10 text-ink/40">
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="5" y="11" width="14" height="9" rx="2" /><path d="M8 11V7a4 4 0 0 1 8 0v4" /></svg>
                                </div>
                                <p class="text-xs font-bold uppercase tracking-wide text-ink/40">Урок {{ $index + 1 }} · {{ $levelCode }}</p>
                                <h3 class="mt-1 pr-8 text-lg font-bold text-ink/50">{{ $lesson->title }}</h3>
                                <p class="mt-3 text-xs font-semibold text-ink/40">Заблокировано — завершите предыдущий урок</p>
                            </div>
                        @else
                            <a
                                href="{{ route('lessons.show', $lesson->id) }}"
                                class="group relative block overflow-hidden rounded-2xl border border-white/60 bg-white/60 p-5 shadow-lg shadow-ink/5 backdrop-blur-xl transition duration-300 ease-out hover:-translate-y-1.5 hover:border-brand/30 hover:shadow-2xl hover:shadow-brand/15"
                                data-reveal
                            >
                                <div class="absolute right-4 top-4 flex h-8 w-8 items-center justify-center rounded-full {{ $isCompleted ? 'bg-green-100 text-green-600' : 'bg-brand/10 text-brand' }}">
                                    @if ($isCompleted)
                                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12" /></svg>
                                    @else
                                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="5 3 19 12 5 21 5 3" /></svg>
                                    @endif
                                </div>

                                <p class="text-xs font-bold uppercase tracking-wide text-brand/70">Урок {{ $index + 1 }} · {{ $levelCode }}</p>
                                <h3 class="mt-1 pr-8 text-lg font-bold text-ink">{{ $lesson->title }}</h3>

                                <div class="mt-3 flex items-center gap-3 text-xs font-semibold text-ink/50">
                                    @if ($lesson->estimated_minutes)
                                        <span class="inline-flex items-center gap-1">
                                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10" /><path d="M12 6v6l4 2" /></svg>
                                            {{ $lesson->estimated_minutes }} мин
                                        </span>
                                    @endif
                                    <x-ui.badge :variant="$isCompleted ? 'success' : 'neutral'">
                                        {{ $isCompleted ? 'Пройден' : ($percent > 0 ? "{$percent}%" : 'Доступен') }}
                                    </x-ui.badge>
                                </div>

                                @if ($percent > 0 && ! $isCompleted)
                                    <div class="mt-3 h-1.5 w-full overflow-hidden rounded-full bg-ink/10">
                                        <div class="h-full rounded-full bg-gradient-to-r from-brand to-sky" style="width: {{ $percent }}%"></div>
                                    </div>
                                @endif
                            </a>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    </div>
@endsection
