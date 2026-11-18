{{-- Одна плитка статистики — переиспользуется 4 раза (слова/уроки/
     упражнения/тесты) с разным accent-цветом. Число — data-counter
     (общий механизм из layouts/app.blade.php: считает от 0 при попадании
     в зону видимости, сам отключается при prefers-reduced-motion). Кольцо
     прогресса — на x-init, потому что это SVG stroke-dashoffset, а не
     ширина: делать для него отдельный вариант data-fill-width ради одной
     карточки на дашборде — overkill, тем более кольцо и так почти всегда
     выше сгиба и появляется вместе с загрузкой страницы. --}}
@props([
    'label',
    'value',
    'icon',
    'accent' => 'brand',
    'progress' => null,
    'delay' => 0,
])

@php
    $accentVar = ['brand' => '--pe-brand', 'sky' => '--pe-sky', 'sun' => '--pe-sun', 'gold' => '--pe-gold', 'skylight' => '--pe-skylight'][$accent] ?? '--pe-brand';
    $isWholeNumber = is_numeric($value) && (int) $value == $value;
@endphp

<x-ui.card class="!p-5 text-center" :hover="true" style="--accent: var({{ $accentVar }}); transition-delay: {{ $delay }}ms">
    <div class="relative mx-auto mb-3 flex h-16 w-16 items-center justify-center">
        @if ($progress !== null)
            <svg class="absolute inset-0 h-16 w-16 -rotate-90" viewBox="0 0 64 64">
                <circle cx="32" cy="32" r="27" fill="none" stroke-width="5" class="acc-ring-bg" />
                <circle
                    cx="32" cy="32" r="27" fill="none" stroke-width="5" stroke-linecap="round"
                    class="acc-ring-fg"
                    stroke-dasharray="170"
                    style="stroke-dashoffset: 170; transition: stroke-dashoffset 1s ease-out"
                    x-data x-init="requestAnimationFrame(() => $el.style.strokeDashoffset = 170 - (170 * {{ $progress }} / 100))"
                />
            </svg>
        @endif
        <span class="acc-icon relative flex h-11 w-11 items-center justify-center rounded-full">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="{{ $icon }}" /></svg>
        </span>
    </div>

    @if ($isWholeNumber)
        <p class="text-3xl font-extrabold text-ink"><span data-counter data-target="{{ (int) $value }}">0</span></p>
    @else
        <p class="text-3xl font-extrabold text-ink">{{ $value }}</p>
    @endif
    <p class="mt-0.5 text-xs font-semibold text-ink/50">{{ $label }}</p>
</x-ui.card>
