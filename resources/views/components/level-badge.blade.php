@props(['level' => null])

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

    $classes = $code ? ($paletteByLevel[strtoupper(substr($code, 0, 2))] ?? 'border border-line bg-surface2 text-ink/50') : 'border border-line bg-surface2 text-ink/50';
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center gap-1 rounded-full px-3 py-1 text-xs font-bold {$classes}"]) }}>
    {{ $code ?? 'Без уровня' }}
</span>
