{{--
    Текстовое поле с подписью и подсветкой ошибки валидации.
    Ошибку можно передать явно (error="...") или она подхватится
    из стандартного $errors по имени поля.
--}}
@props([
    'label' => null,
    'name' => '',
    'type' => 'text',
    'error' => null,
])

@php
    $errorMessage = $error ?: ($errors->first($name) ?: null);
@endphp

<div class="w-full">
    @if ($label)
        <label for="{{ $name }}" class="mb-1.5 block text-sm font-semibold text-ink">{{ $label }}</label>
    @endif

    <input
        id="{{ $name }}"
        name="{{ $name }}"
        type="{{ $type }}"
        {{ $attributes->merge([
            'class' => 'w-full rounded-xl border-2 bg-armor2/60 px-4 py-2.5 text-sm text-ink placeholder:text-ink/30 backdrop-blur transition focus:outline-none focus:ring-4 '
                .($errorMessage
                    ? 'border-red-400 focus:border-red-500 focus:ring-red-500/15'
                    : 'border-white/10 focus:border-sky focus:ring-sky/15'),
        ]) }}
    >

    @if ($errorMessage)
        <p class="mt-1.5 flex items-center gap-1 text-sm font-medium text-red-600">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" class="shrink-0"><circle cx="12" cy="12" r="10" /><path d="M12 8v5M12 16h.01" /></svg>
            {{ $errorMessage }}
        </p>
    @endif
</div>
