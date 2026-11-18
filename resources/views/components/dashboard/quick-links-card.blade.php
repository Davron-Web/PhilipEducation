{{-- Быстрый доступ к разделам сайта — у каждого свой accent-цвет, иначе
     все восемь карточек сливаются в одинаковый ряд. Перенесено из старой
     версии дашборда без изменений в логике. --}}
@props(['delay' => 0])

@php
    $sections = [
        ['label' => 'Уроки', 'route' => 'lessons.index', 'accent' => '--pe-brand', 'icon' => 'M8 6h13M8 12h13M8 18h13M3 6h.01M3 12h.01M3 18h.01'],
        ['label' => 'Грамматика', 'route' => 'grammartopics.index', 'accent' => '--pe-sky', 'icon' => 'M5 8h9M9 4v4M12 8c-1 3.5-3.5 6-7 8M6 12c1.5 1.5 4 2.5 6 3M14 20l4-9 4 9M15.5 17h5'],
        ['label' => 'Словарь', 'route' => 'words.index', 'accent' => '--pe-gold', 'icon' => 'M4 19.5A2.5 2.5 0 0 1 6.5 17H20M4 19.5A2.5 2.5 0 0 0 6.5 22H20V4H6.5A2.5 2.5 0 0 0 4 6.5v13Z'],
        ['label' => 'Выражения', 'route' => 'expressions.index', 'accent' => '--pe-sun', 'icon' => 'M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v10Z'],
        ['label' => 'Упражнения', 'route' => 'exercises.index', 'accent' => '--pe-skylight', 'icon' => 'M9 11l3 3L22 4M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11'],
        ['label' => 'Тесты', 'route' => 'tests.index', 'accent' => '--pe-brand', 'icon' => 'M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2M9 5a2 2 0 0 0 2 2h2a2 2 0 0 0 2-2M9 5a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2M9 14l2 2 4-4'],
        ['label' => 'IELTS', 'route' => 'ielts.index', 'accent' => '--pe-sun', 'icon' => 'M12 2a10 10 0 1 0 0 20 10 10 0 0 0 0-20ZM2 12h20M12 2a15 15 0 0 1 4 10 15 15 0 0 1-4 10 15 15 0 0 1-4-10 15 15 0 0 1 4-10Z'],
        ['label' => 'Книги', 'route' => 'books.index', 'accent' => '--pe-sky', 'icon' => 'M12 6.5C10.5 5 8 4 4 4v14c4 0 6.5 1 8 2.5V6.5ZM12 6.5C13.5 5 16 4 20 4v14c-4 0-6.5 1-8 2.5V6.5Z'],
    ];
@endphp

<x-ui.card class="!p-6" :hover="true" style="transition-delay: {{ $delay }}ms">
    <h3 class="mb-4 text-sm font-bold uppercase tracking-wide text-ink/50">Разделы</h3>
    <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">
        @foreach ($sections as $section)
            <a
                href="{{ route($section['route']) }}"
                class="acc-tint group flex flex-col items-center gap-2 rounded-xl border border-line p-4 text-center transition hover:-translate-y-0.5 hover:shadow-soft"
                style="--accent: var({{ $section['accent'] }})"
            >
                <span class="acc-icon flex h-11 w-11 items-center justify-center rounded-full">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="{{ $section['icon'] }}" /></svg>
                </span>
                <span class="text-xs font-semibold text-ink/70">{{ $section['label'] }}</span>
            </a>
        @endforeach
    </div>
</x-ui.card>
