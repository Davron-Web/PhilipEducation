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
        'primary' => 'border border-sky/50 bg-sky/10 text-sky shadow-[0_0_14px_rgba(34,211,238,.18),inset_0_0_14px_rgba(34,211,238,.08)] hover:bg-sky/20 hover:shadow-[0_0_26px_rgba(34,211,238,.45)] hover:-translate-y-0.5',
        'accent' => 'bg-gradient-to-b from-[#FFE75E] via-sun to-[#E0B400] text-[#171325] shadow-[0_0_26px_rgba(255,215,0,.4)] hover:-translate-y-0.5 hover:shadow-[0_0_40px_rgba(255,215,0,.6)]',
        'outline' => 'border border-teal-400/40 bg-transparent text-skylight hover:border-skylight hover:bg-skylight/10 hover:-translate-y-0.5',
        'ghost' => 'text-ink/70 hover:bg-white/5 hover:text-sky',
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
