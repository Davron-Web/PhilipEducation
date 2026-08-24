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
        'A1' => 'bg-sky/15 text-sky-700',
        'A2' => 'bg-skylight/30 text-sky-800',
        'B1' => 'bg-brand/10 text-brand',
        'B2' => 'bg-brand/15 text-ink',
        'C1' => 'bg-ink/10 text-ink',
        'C2' => 'bg-ink/15 text-ink',
    ];

    $variants = [
        'success' => 'bg-green-100 text-green-700',
        'danger' => 'bg-red-100 text-red-700',
        'accent' => 'bg-sun/20 text-amber-700',
        'neutral' => 'bg-ink/5 text-ink/60',
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
