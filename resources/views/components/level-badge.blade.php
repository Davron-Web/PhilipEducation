@props(['level' => null])

@php
    $code = is_object($level) ? ($level->code ?? $level->name ?? null) : $level;

    $color = match (true) {
        $code === null => 'secondary',
        str_starts_with($code, 'A1') => 'success',
        str_starts_with($code, 'A2') => 'info',
        str_starts_with($code, 'B1') => 'primary',
        str_starts_with($code, 'B2') => 'accent',
        str_starts_with($code, 'C1') || str_starts_with($code, 'C2') => 'dark',
        default => 'secondary',
    };
@endphp

<span {{ $attributes->merge(['class' => "badge text-bg-{$color} rounded-pill fw-semibold"]) }}>
    {{ $code ?? 'Unrated' }}
</span>
