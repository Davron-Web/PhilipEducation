{{--
    Переключатель языка интерфейса. Ставит выбранную локаль в сессию
    (маршрут locale.switch) и возвращает пользователя на ту же страницу.
    Живёт внутри Alpine-корня шапки, поэтому использует его состояние
    localeOpen.
--}}
@props([
    'locales' => [],
    'current' => 'ru',
])

<div class="relative">
    <button
        type="button"
        @click="localeOpen = !localeOpen"
        class="flex h-10 items-center gap-1.5 rounded px-2.5 text-white/85 transition hover:bg-white/10 hover:text-gold"
        :aria-expanded="localeOpen"
        aria-label="{{ __('site.header.language') }}"
    >
        <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <circle cx="12" cy="12" r="10" /><path d="M2 12h20" />
            <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z" />
        </svg>
        <span class="text-xs font-bold uppercase tracking-wider">{{ $current }}</span>
    </button>

    <div
        x-show="localeOpen"
        @click.outside="localeOpen = false"
        x-transition:enter="transition ease-out duration-150"
        x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
        x-transition:leave="transition ease-in duration-100"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="absolute right-0 top-12 z-50 w-44 origin-top-right overflow-hidden rounded border border-white/10 bg-navy2 p-1.5 shadow-xl"
        style="display: none;"
    >
        @foreach ($locales as $code => $name)
            <a
                href="{{ route('locale.switch', $code) }}"
                class="flex items-center justify-between rounded px-3 py-2 text-sm font-medium transition {{ $code === $current ? 'text-gold' : 'text-white/80 hover:bg-white/10 hover:text-gold' }}"
            >
                {{ $name }}
                @if ($code === $current)
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><path d="M20 6 9 17l-5-5" /></svg>
                @endif
            </a>
        @endforeach
    </div>
</div>
