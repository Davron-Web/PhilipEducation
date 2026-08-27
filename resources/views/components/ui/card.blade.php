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
    $classes = 'relative rounded-2xl border border-white/10 bg-gradient-to-b from-armor2/80 to-armor/80 p-6 shadow-lg shadow-black/20 backdrop-blur-xl transition duration-300 ease-out'
        .($hover ? ' hover:-translate-y-1.5 hover:border-sky/40 hover:shadow-2xl hover:shadow-sky/15' : '');
@endphp

<div {{ $attributes->merge(['class' => $classes]) }} @if ($reveal) data-reveal @endif>
    {{ $slot }}
</div>
