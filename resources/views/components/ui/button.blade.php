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
        'primary' => 'bg-gradient-to-r from-brand to-sky text-white shadow-lg shadow-brand/25 hover:shadow-xl hover:-translate-y-0.5',
        'accent' => 'bg-sun text-ink shadow-lg shadow-sun/30 hover:-translate-y-0.5 hover:shadow-[0_0_28px_rgba(250,204,21,0.55)]',
        'outline' => 'border-2 border-brand/15 bg-white/70 text-ink backdrop-blur hover:border-brand hover:bg-white hover:-translate-y-0.5',
        'ghost' => 'text-ink/70 hover:bg-brand/5 hover:text-brand',
    ];

    $classes = 'inline-flex items-center justify-center gap-2 rounded-xl font-bold transition duration-300 ease-out '
        .'focus:outline-none focus:ring-4 focus:ring-brand/20 disabled:cursor-not-allowed disabled:opacity-50 disabled:hover:translate-y-0 '
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
