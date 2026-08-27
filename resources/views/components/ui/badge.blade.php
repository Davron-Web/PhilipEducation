{{--
    Универсальный бейдж-пилюля.
    variant: level (нужен проп level — код A1..C2) | success | danger | accent | neutral
--}}
@props([
    'variant' => 'neutral',
    'level' => null,
])

@php
    $code = is_object($level) ? ($level->code ?? $level->name ?? null) : $level;

    $paletteByLevel = [
        'A1' => 'border border-sky/30 bg-sky/10 text-sky',
        'A2' => 'border border-skylight/30 bg-skylight/10 text-skylight',
        'B1' => 'border border-brand/40 bg-brand/10 text-brand',
        'B2' => 'border border-brand/40 bg-brand/15 text-[#C4B5FD]',
        'C1' => 'border border-sun/30 bg-sun/10 text-sun',
        'C2' => 'border border-sun/30 bg-sun/15 text-sun',
    ];

    $variants = [
        'success' => 'border border-green-500/30 bg-green-500/10 text-green-400',
        'danger' => 'border border-red-500/30 bg-red-500/10 text-red-400',
        'accent' => 'border border-sun/30 bg-sun/10 text-sun',
        'neutral' => 'border border-white/10 bg-white/5 text-ink/50',
    ];

    if ($variant === 'level') {
        $classes = $code ? ($paletteByLevel[strtoupper(substr($code, 0, 2))] ?? $variants['neutral']) : $variants['neutral'];
        $text = $code ?? 'Без уровня';
    } else {
        $classes = $variants[$variant] ?? $variants['neutral'];
        $text = null;
    }
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center gap-1 rounded-full px-3 py-1 text-xs font-bold {$classes}"]) }}>
    {{ $text ?? $slot }}
</span>
