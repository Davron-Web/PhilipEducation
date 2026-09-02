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
    $classes = 'relative rounded-3xl border border-line bg-armor2 p-6 shadow-soft'
        .($hover ? ' card-lift hover:border-brand/30' : '');
@endphp

<div {{ $attributes->merge(['class' => $classes]) }} @if ($reveal) data-reveal @endif>
    {{ $slot }}
</div>
