{{--
    Карточка: белая (тёмная в night-режиме) поверхность со скруглением,
    мягкой тенью и лёгким подъёмом при наведении.
    reveal: добавляет data-reveal — карточка проявляется при скролле (см. app.blade.php).
    hover:  подъём + смена тени при наведении (класс card-lift, см. app.blade.php).
--}}
@props([
    'reveal' => true,
    'hover' => true,
])

@php
    $classes = 'relative overflow-hidden rounded-2xl border border-line bg-armor2 p-6 shadow-soft'
        .($hover ? ' card-lift hover:border-brand/30 before:absolute before:inset-x-0 before:top-0 before:h-[3px] before:origin-left before:scale-x-0 before:bg-gradient-to-r before:from-[var(--pe-brand)] before:to-[var(--pe-sky)] before:transition-transform before:duration-300 before:content-[\'\'] hover:before:scale-x-100' : '');
@endphp

<div {{ $attributes->merge(['class' => $classes]) }} @if ($reveal) data-reveal @endif>
    {{ $slot }}
</div>
