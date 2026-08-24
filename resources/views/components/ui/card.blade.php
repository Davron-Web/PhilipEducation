{{--
    Карточка в стиле glassmorphism: полупрозрачный фон, blur, мягкая граница.
    reveal: добавляет data-reveal — карточка проявляется при скролле (см. app.blade.php).
    hover:  подъём и свечение по краю при наведении.
--}}
@props([
    'reveal' => true,
    'hover' => true,
])

@php
    $classes = 'relative rounded-2xl border border-white/60 bg-white/60 p-6 shadow-lg shadow-ink/5 backdrop-blur-xl transition duration-300 ease-out'
        .($hover ? ' hover:-translate-y-1.5 hover:border-brand/30 hover:shadow-2xl hover:shadow-brand/15' : '');
@endphp

<div {{ $attributes->merge(['class' => $classes]) }} @if ($reveal) data-reveal @endif>
    {{ $slot }}
</div>
