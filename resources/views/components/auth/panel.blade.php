{{--
    Общая обёртка для страниц авторизации: логотип, заголовок, подзаголовок,
    форма на карточке glassmorphism по центру экрана, ссылка снизу (slot footer).
--}}
@props([
    'title' => '',
    'subtitle' => null,
])

<div class="relative flex min-h-[calc(100vh-4rem)] items-center justify-center px-4 py-16">
    <div class="w-full max-w-md" data-reveal>
        <div class="mb-8 text-center">
            <a href="{{ url('/') }}" class="inline-flex h-14 w-14 items-center justify-center rounded-2xl bg-gradient-to-br from-brand to-sky text-white shadow-lg shadow-brand/30">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M6 6l2 3" /><path d="M18 6l-2 3" />
                    <ellipse cx="12" cy="13" rx="7" ry="8" />
                    <circle cx="9" cy="12" r="1.4" fill="currentColor" stroke="none" />
                    <circle cx="15" cy="12" r="1.4" fill="currentColor" stroke="none" />
                </svg>
            </a>
            <h1 class="mt-4 text-2xl font-extrabold text-ink">{{ $title }}</h1>
            @if ($subtitle)
                <p class="mt-1 text-sm text-ink/60">{{ $subtitle }}</p>
            @endif
        </div>

        <x-ui.card :reveal="false">
            {{ $slot }}
        </x-ui.card>

        @isset($footer)
            <p class="mt-6 text-center text-sm text-ink/60">{{ $footer }}</p>
        @endisset
    </div>
</div>
