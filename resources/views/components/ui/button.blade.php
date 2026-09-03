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
        'sm' => 'px-4 py-2 text-xs',
        'md' => 'px-6 py-2.5 text-xs',
        'lg' => 'px-8 py-3.5 text-sm',
    ];

    $variants = [
        'primary' => 'bg-gradient-to-br from-[#C9A961] to-[#D4AF37] text-navy shadow-soft hover:-translate-y-0.5 hover:shadow-softLg',
        'accent' => 'bg-sun text-white shadow-soft hover:-translate-y-0.5 hover:shadow-softLg',
        'outline' => 'border-2 border-line bg-armor2 text-ink hover:border-brand/50 hover:bg-surface2',
        'ghost' => 'text-ink/70 hover:bg-surface2 hover:text-brand',
    ];

    $classes = 'inline-flex items-center justify-center gap-2 rounded font-bold uppercase tracking-wider transition duration-300 ease-out '
        .'focus:outline-none focus:ring-4 focus:ring-brand/15 disabled:cursor-not-allowed disabled:opacity-50 disabled:hover:translate-y-0 '
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
