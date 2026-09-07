{{-- Результаты поиска, сгруппированные по разделам. --}}
@extends('layouts.app')

@section('title', $query !== '' ? 'Поиск: '.$query : 'Поиск')
@section('page_title', 'Поиск')

@php
    $labels = [
        'lessons' => ['Уроки', 'M4 19.5A2.5 2.5 0 0 1 6.5 17H20M4 19.5A2.5 2.5 0 0 0 6.5 22H20V4H6.5A2.5 2.5 0 0 0 4 6.5v13Z'],
        'grammar' => ['Грамматика', 'M12 20h9M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4Z'],
        'words' => ['Словарь', 'M12 7v14M3 18a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1h5a4 4 0 0 1 4 4 4 4 0 0 1 4-4h5a1 1 0 0 1 1 1v13a1 1 0 0 1-1 1h-6a3 3 0 0 0-3 3 3 3 0 0 0-3-3Z'],
        'expressions' => ['Выражения', 'M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2Z'],
        'books' => ['Книги', 'M4 19.5A2.5 2.5 0 0 1 6.5 17H20M4 19.5A2.5 2.5 0 0 0 6.5 22H20V4H6.5A2.5 2.5 0 0 0 4 6.5v13Z'],
        'tests' => ['Тесты', 'M9 11l3 3L22 4M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11'],
    ];
@endphp

@section('content')
    <div class="mx-auto max-w-4xl px-4 py-10 sm:px-6 lg:px-8">

        <form method="GET" action="{{ route('search') }}" class="mb-8">
            <label for="search-input" class="mb-2 block font-display text-3xl font-semibold text-ink">Поиск по материалам</label>

            <div class="flex gap-2">
                <div class="relative flex-1">
                    <svg class="pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-ink/40" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="11" cy="11" r="7" /><path d="m21 21-4.3-4.3" /></svg>
                    <input
                        id="search-input"
                        type="search"
                        name="q"
                        value="{{ $query }}"
                        autofocus
                        placeholder="Слово, тема, урок или книга…"
                        class="w-full rounded-lg border border-line bg-armor2 py-3 pl-11 pr-4 text-ink placeholder:text-ink/30 focus:border-brand focus:outline-none focus:ring-4 focus:ring-brand/15"
                    >
                </div>
                <x-ui.button type="submit">Найти</x-ui.button>
            </div>
        </form>

        @if ($query === '')
            <x-ui.card :hover="false" class="py-12 text-center">
                <p class="text-ink/60">Введите слово или тему — поищу в уроках, грамматике, словаре, выражениях, книгах и тестах.</p>
            </x-ui.card>
        @elseif (mb_strlen($query) < 2)
            <x-ui.card :hover="false" class="py-12 text-center">
                <p class="font-semibold text-ink">Слишком короткий запрос</p>
                <p class="mt-1 text-sm text-ink/50">Нужно хотя бы два символа — по одному находится половина сайта.</p>
            </x-ui.card>
        @elseif ($total === 0)
            <x-ui.card :hover="false" class="py-12 text-center">
                <p class="font-semibold text-ink">Ничего не нашлось по запросу «{{ $query }}»</p>
                <p class="mt-1 text-sm text-ink/50">Проверьте раскладку клавиатуры или попробуйте короче — например, «present» вместо «present perfect continuous».</p>
            </x-ui.card>
        @else
            <p class="mb-6 text-sm font-semibold text-ink/50">
                Найдено {{ $total }} по запросу «{{ $query }}»
            </p>

            <div class="space-y-8">
                @foreach ($groups as $key => $items)
                    @php [$label, $icon] = $labels[$key] ?? [$key, '']; @endphp

                    <section>
                        <h2 class="mb-3 flex items-center gap-2 text-sm font-bold uppercase tracking-wider text-ink/50">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-brand"><path d="{{ $icon }}" /></svg>
                            {{ $label }}
                            <span class="text-ink/30">{{ $items->count() }}</span>
                        </h2>

                        <div class="divide-y divide-line overflow-hidden rounded-2xl border border-line bg-armor2">
                            @foreach ($items as $item)
                                <a href="{{ $item['url'] }}" class="flex items-center justify-between gap-4 px-5 py-3.5 transition hover:bg-surface2">
                                    <span class="min-w-0">
                                        <span class="block truncate font-semibold text-ink">{{ $item['title'] }}</span>
                                        @if ($item['subtitle'])
                                            <span class="block truncate text-sm text-ink/50">{{ $item['subtitle'] }}</span>
                                        @endif
                                    </span>
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" class="shrink-0 text-ink/30"><path d="M9 18l6-6-6-6" /></svg>
                                </a>
                            @endforeach
                        </div>
                    </section>
                @endforeach
            </div>
        @endif
    </div>
@endsection
