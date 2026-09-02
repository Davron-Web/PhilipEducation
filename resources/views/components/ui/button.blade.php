{{--
    Универсальная кнопка. Рендерится как <a>, если передан href, иначе как <button>.
    variant: primary | accent | outline | ghost
    size:    sm | md | lg
--}}
@props([
    'variant' => 'primary',
    'size' => 'md',
    'href' => null,
    'type' => 'button',
])

@php
    $sizes = [
        'sm' => 'px-4 py-2 text-sm',
        'md' => 'px-5 py-2.5 text-sm',
        'lg' => 'px-7 py-3.5 text-base',
    ];

    $variants = [
        'primary' => 'bg-brand text-white shadow-soft hover:bg-brand/90',
        'accent' => 'bg-sun text-white shadow-soft hover:bg-sun/90',
        'outline' => 'border border-line bg-armor2 text-ink hover:border-brand/40 hover:bg-surface2',
        'ghost' => 'text-ink/70 hover:bg-surface2 hover:text-brand',
    ];

    $classes = 'inline-flex items-center justify-center gap-2 rounded-lg font-semibold transition duration-150 ease-out '
        .'focus:outline-none focus:ring-4 focus:ring-brand/15 disabled:cursor-not-allowed disabled:opacity-50 '
        .($sizes[$size] ?? $sizes['md']).' '
        .($variants[$variant] ?? $variants['primary']);
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </button>
@endif
