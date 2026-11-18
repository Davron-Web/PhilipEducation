{{--
    Универсальный бейдж-пилюля.
    variant: level (нужен проп level — код A1..C2) | success | danger | accent | neutral
--}}
@props([
    'variant' => 'neutral',
    'level' => null,
    'register' => null,
])

@php
    $code = is_object($level) ? ($level->code ?? $level->name ?? null) : $level;

    $paletteByLevel = [
        'A1' => 'border border-sky/30 bg-sky/10 text-sky',
        'A2' => 'border border-skylight/30 bg-skylight/10 text-skylight',
        'B1' => 'border border-brand/40 bg-brand/10 text-brand',
        'B2' => 'border border-brand/40 bg-brand/15 text-brand',
        'C1' => 'border border-sun/30 bg-sun/10 text-sun',
        'C2' => 'border border-sun/30 bg-sun/15 text-sun',
    ];

    // formal/neutral/informal — регистр речи (см. Expression::register).
    // Формальные связки годятся для эссе, неформальные — только для
    // разговора, поэтому цвета нарочно разные: sky (спокойный, "безопасно"
    // для письма) → серый (нейтрально) → sun (тёплый, "осторожно, разговорное").
    $paletteByRegister = [
        'formal' => 'border border-sky/30 bg-sky/10 text-sky',
        'neutral' => 'border border-line bg-surface2 text-ink/50',
        'informal' => 'border border-sun/30 bg-sun/10 text-sun',
    ];

    $variants = [
        'success' => 'border border-green-500/30 bg-green-500/10 text-green-600 dark:text-green-400',
        'danger' => 'border border-red-500/30 bg-red-500/10 text-red-600 dark:text-red-400',
        'accent' => 'border border-sun/30 bg-sun/10 text-sun',
        'neutral' => 'border border-line bg-surface2 text-ink/50',
    ];

    if ($variant === 'level') {
        $classes = $code ? ($paletteByLevel[strtoupper(substr($code, 0, 2))] ?? $variants['neutral']) : $variants['neutral'];
        $text = $code ?? 'Без уровня';
    } elseif ($variant === 'register') {
        $classes = $register ? ($paletteByRegister[$register] ?? $variants['neutral']) : $variants['neutral'];
        $text = $register ? __('site.expressions.register.'.$register) : null;
    } else {
        $classes = $variants[$variant] ?? $variants['neutral'];
        $text = null;
    }
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center gap-1 rounded-full px-3 py-1 text-xs font-bold {$classes}"]) }}>
    {{ $text ?? $slot }}
</span>
