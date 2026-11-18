@extends('layouts.app')

@section('title', 'Лидеры')
@section('page_title', 'Лидеры')
@section('meta_description', 'Рейтинг учеников Philip Education по очкам — за неделю, за месяц и за всё время.')

@php
    // Приглушённые медальные цвета — не «кислотные»: 15%-фон + сам цвет
    // текста/бейджа. Литеральные Tailwind-цвета, не токен gold проекта:
    // --pe-gold здесь равен --pe-sky (синий) с отдельной задачи "убрать
    // золото из палитры" — для самого места медали смысл важнее общего
    // правила, поэтому это осознанное точечное исключение, не забытый token.
    $medal = [
        1 => ['ring' => 'ring-amber-400/50', 'badge' => 'bg-amber-400/15 text-amber-600 dark:text-amber-400', 'size' => 'h-20 w-20 text-2xl', 'lift' => ''],
        2 => ['ring' => 'ring-slate-400/40', 'badge' => 'bg-slate-400/15 text-slate-500 dark:text-slate-300', 'size' => 'h-16 w-16 text-lg', 'lift' => 'mt-6'],
        3 => ['ring' => 'ring-orange-700/40', 'badge' => 'bg-orange-700/15 text-orange-700 dark:text-orange-500', 'size' => 'h-16 w-16 text-lg', 'lift' => 'mt-6'],
    ];
    $tabs = ['week' => 'За неделю', 'month' => 'За месяц', 'all' => 'За всё время'];
@endphp

@section('content')
    <div class="mx-auto max-w-4xl px-4 py-10 sm:px-6 lg:px-8" x-data="{ tab: 'week' }">
        <div class="mb-8" data-reveal>
            <h1 class="text-3xl font-extrabold text-ink sm:text-4xl">Лидеры</h1>
            <p class="mt-1 text-ink/60">Рейтинг учеников платформы по очкам.</p>
        </div>

        <div class="mb-6 inline-flex rounded-full border border-line bg-armor2 p-1">
            @foreach ($tabs as $key => $label)
                <button
                    type="button"
                    @click="tab = '{{ $key }}'"
                    class="rounded-full px-4 py-2 text-sm font-bold transition"
                    :class="tab === '{{ $key }}' ? 'bg-brand text-white' : 'text-ink/60 hover:text-ink'"
                >{{ $label }}</button>
            @endforeach
        </div>

        @foreach ($tabs as $period => $label)
            <div x-show="tab === '{{ $period }}'">
                @php $board = $boards[$period]; @endphp

                @if ($board->isEmpty())
                    <x-ui.card :hover="false" class="py-16 text-center">
                        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" class="mx-auto mb-3 text-ink/25"><path d="M8 21h8M12 17v4M7 4h10v5a5 5 0 0 1-10 0V4Z" /><path d="M7 6H4a2 2 0 0 0 0 4h.5M17 6h3a2 2 0 0 1 0 4h-.5" /></svg>
                        <p class="text-sm text-ink/50">Пока никто не набрал очков за этот период.<br>Пройдите урок или тест — и станьте первым.</p>
                    </x-ui.card>
                @else
                    {{-- Подиум: топ-3, первое место по центру и крупнее. --}}
                    <div class="mb-8 flex items-end justify-center gap-4 sm:gap-8">
                        @foreach ([2, 1, 3] as $place)
                            @php $entry = $board->firstWhere('rank', $place); @endphp
                            @if ($entry)
                                <div class="flex flex-col items-center {{ $medal[$place]['lift'] }}">
                                    <div class="relative">
                                        <span class="flex {{ $medal[$place]['size'] }} items-center justify-center overflow-hidden rounded-full bg-gradient-to-br from-brand to-sky font-extrabold text-white ring-4 {{ $medal[$place]['ring'] }}">
                                            @if ($entry->user->avatar_url)
                                                <img src="{{ $entry->user->avatar_url }}" alt="" class="h-full w-full object-cover">
                                            @else
                                                {{ $entry->user->initial }}
                                            @endif
                                        </span>
                                        <span class="absolute -bottom-1 -right-1 flex h-6 w-6 items-center justify-center rounded-full text-xs font-extrabold ring-2 ring-armor2 {{ $medal[$place]['badge'] }}">{{ $place }}</span>
                                    </div>
                                    <p class="mt-3 max-w-[110px] truncate text-center text-sm font-bold text-ink">{{ $entry->user->name }}</p>
                                    <p class="mt-0.5 rounded-full px-2.5 py-0.5 text-sm font-extrabold {{ $medal[$place]['badge'] }}">{{ number_format($entry->points, 0, '', ' ') }}</p>
                                </div>
                            @endif
                        @endforeach
                    </div>

                    {{-- Места 4-50. --}}
                    @if ($board->count() > 3)
                        <x-ui.card :hover="false" class="space-y-1">
                            @foreach ($board->skip(3) as $row)
                                <x-leaderboard.row
                                    :rank="$row->rank"
                                    :user="$row->user"
                                    :points="$row->points"
                                    :change="$row->change"
                                    :highlight="$row->user->id === $currentUserId"
                                />
                            @endforeach

                            @if ($me[$period])
                                <div class="my-2 flex items-center gap-3 px-3 text-xs font-bold uppercase tracking-wider text-ink/30">
                                    <span class="h-px flex-1 bg-line"></span>
                                    Ваше место
                                    <span class="h-px flex-1 bg-line"></span>
                                </div>

                                @if ($me[$period]['rank'])
                                    <x-leaderboard.row
                                        :rank="$me[$period]['rank']"
                                        :user="$me[$period]['user']"
                                        :points="$me[$period]['points']"
                                        :change="$me[$period]['change']"
                                        :highlight="true"
                                    />
                                @else
                                    <p class="px-3 pb-1 text-sm text-ink/50">У вас пока нет очков за этот период — самое время начать.</p>
                                @endif
                            @endif
                        </x-ui.card>
                    @endif
                @endif
            </div>
        @endforeach
    </div>
@endsection
