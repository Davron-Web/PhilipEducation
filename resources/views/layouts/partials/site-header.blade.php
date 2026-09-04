{{-- Единая шапка сайта: логотип, навигация, поиск, streak и меню профиля.
     Для гостя показывает маркетинговые кнопки, для авторизованного — полное меню. --}}
@php
    $navItems = [
        ['label' => __('site.nav.lessons'), 'url' => route('lessons.index'), 'active' => request()->routeIs('lessons.*')],
        ['label' => __('site.nav.grammar'), 'url' => route('grammartopics.index'), 'active' => request()->routeIs('grammartopics.*')],
        ['label' => __('site.nav.vocabulary'), 'url' => route('words.index'), 'active' => request()->routeIs('words.*')],
        ['label' => __('site.nav.expressions'), 'url' => route('expressions.index'), 'active' => request()->routeIs('expressions.*')],
        ['label' => __('site.nav.exercises'), 'url' => route('exercises.index'), 'active' => request()->routeIs('exercises.*')],
        ['label' => __('site.nav.tests'), 'url' => route('tests.index'), 'active' => request()->routeIs('tests.*')],
        ['label' => __('site.nav.ielts'), 'url' => route('ielts.index'), 'active' => request()->routeIs('ielts.*')],
        ['label' => __('site.nav.books'), 'url' => route('books.index'), 'active' => request()->routeIs('books.*')],
        ['label' => __('site.nav.achievements'), 'url' => route('achievements.index'), 'active' => request()->routeIs('achievements.*')],
    ];

    $unreadCount = auth()->check()
        ? auth()->user()->notifications()->where('is_read', false)->count()
        : 0;

    $locales = App\Http\Middleware\SetLocale::SUPPORTED;
    $currentLocale = app()->getLocale();
@endphp

<header
    x-data="{ mobileOpen: false, userMenuOpen: false, searchOpen: false, localeOpen: false, scrolled: false }"
    @keydown.escape.window="mobileOpen = false; userMenuOpen = false; searchOpen = false; localeOpen = false"
    @scroll.window="scrolled = window.scrollY > 40"
    class="sticky top-0 z-50 bg-navy transition-shadow"
    :class="scrolled ? 'shadow-lg shadow-black/20' : ''"
>
    <nav class="mx-auto flex h-16 max-w-7xl items-center justify-between gap-3 px-4 sm:px-6 lg:px-8" aria-label="{{ __('site.nav.main') }}">
        {{-- Логотип --}}
        <a href="{{ auth()->check() ? route('user.dashboard') : url('/') }}" class="flex shrink-0 items-center gap-2 group">
            <span class="flex h-9 w-9 items-center justify-center rounded bg-gold text-navy">
                <x-owl-mark :size="20" />
            </span>
            <span class="hidden font-display text-lg font-semibold text-white sm:block">
                Philip <span class="text-gold">Education</span>
            </span>
        </a>

        @auth
            {{-- Десктоп-навигация (авторизован) --}}
            <div class="hidden items-center gap-0.5 xl:flex">
                @foreach ($navItems as $item)
                    <a
                        href="{{ $item['url'] }}"
                        class="whitespace-nowrap rounded px-2.5 py-2 text-sm font-semibold transition {{ $item['active'] ? 'text-gold' : 'text-white hover:text-gold' }}"
                    >{{ $item['label'] }}</a>
                @endforeach
            </div>

            <div class="flex items-center gap-1">
                {{-- Поиск --}}
                <div class="relative hidden sm:block">
                    <button
                        type="button"
                        @click="searchOpen = !searchOpen"
                        class="flex h-10 w-10 items-center justify-center rounded text-white/85 transition hover:bg-white/10 hover:text-gold"
                        aria-label="{{ __('site.header.search') }}"
                    >
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="11" cy="11" r="7" /><path d="m21 21-4.3-4.3" /></svg>
                    </button>
                    <div
                        x-show="searchOpen"
                        @click.outside="searchOpen = false"
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 -translate-y-1"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100 translate-y-0"
                        x-transition:leave-end="opacity-0 -translate-y-1"
                        class="absolute right-0 top-12 w-72 rounded border border-white/10 bg-navy2 p-2 shadow-xl"
                        style="display: none;"
                    >
                        {{-- Поиск пока без обработчика — раздел поиска ещё не реализован --}}
                        <input
                            type="search"
                            placeholder="{{ __('site.header.search_placeholder') }}"
                            class="w-full rounded border border-white/10 bg-navy px-3 py-2 text-sm text-white placeholder:text-white/30 focus:border-gold focus:outline-none focus:ring-2 focus:ring-gold/20"
                        >
                    </div>
                </div>

                {{-- Уведомления --}}
                <a
                    href="{{ route('notifications.index') }}"
                    class="relative flex h-10 w-10 items-center justify-center rounded text-white/85 transition hover:bg-white/10 hover:text-gold"
                    aria-label="{{ __('site.header.notifications') }}{{ $unreadCount > 0 ? ' ('.__('site.header.notifications_unread', ['count' => $unreadCount]).')' : '' }}"
                >
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9" /><path d="M10.3 21a1.94 1.94 0 0 0 3.4 0" /></svg>
                    @if ($unreadCount > 0)
                        <span class="absolute right-1.5 top-1.5 flex h-4 min-w-4 items-center justify-center rounded-full bg-gold px-1 text-[10px] font-extrabold text-navy">
                            {{ $unreadCount > 9 ? '9+' : $unreadCount }}
                        </span>
                    @endif
                </a>

                {{-- Streak: показываем только если контроллер прислал значение --}}
                @isset($streak)
                    <div class="hidden items-center gap-1.5 rounded-full border border-gold/30 bg-gold/10 px-3 py-1.5 text-sm font-bold text-gold sm:flex" title="{{ __('site.header.streak_title') }}">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 2c1 3-2 4-2 7a4 4 0 0 0 8 0c0-1-.4-2-1-3 2 1 3 3.5 3 6a7 7 0 1 1-14 0c0-4 2-6 3-7 1-1 2-2 3-3Z" /></svg>
                        {{ $streak }}
                    </div>
                @endisset

                <x-locale-switcher :locales="$locales" :current="$currentLocale" />

                {{-- Переключатель темы --}}
                <button
                    type="button"
                    id="themeToggle"
                    aria-label="{{ __('site.header.theme_toggle') }}"
                    class="flex h-10 w-10 items-center justify-center rounded text-white/85 transition hover:bg-white/10 hover:text-gold"
                >
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" class="hidden dark:block"><circle cx="12" cy="12" r="4" /><path d="M12 2v2M12 20v2M4.9 4.9l1.4 1.4M17.7 17.7l1.4 1.4M2 12h2M20 12h2M4.9 19.1l1.4-1.4M17.7 6.3l1.4-1.4" /></svg>
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" class="dark:hidden"><path d="M21 12.8A9 9 0 1 1 11.2 3a7 7 0 0 0 9.8 9.8Z" /></svg>
                </button>

                {{-- Меню профиля --}}
                <div class="relative">
                    <button
                        type="button"
                        @click="userMenuOpen = !userMenuOpen"
                        class="flex items-center gap-2 rounded border border-white/10 py-1 pl-1 pr-2 transition hover:border-gold/40"
                    >
                        <span class="flex h-8 w-8 items-center justify-center rounded-full bg-gold text-sm font-bold text-navy">
                            {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
                        </span>
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" class="hidden text-white/50 sm:block"><path d="m6 9 6 6 6-6" /></svg>
                    </button>

                    <div
                        x-show="userMenuOpen"
                        @click.outside="userMenuOpen = false"
                        x-transition:enter="transition ease-out duration-150"
                        x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
                        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-100"
                        x-transition:leave-start="opacity-100 scale-100"
                        x-transition:leave-end="opacity-0 scale-95"
                        class="absolute right-0 top-12 w-56 origin-top-right overflow-hidden rounded border border-white/10 bg-navy2 p-1.5 shadow-xl"
                        style="display: none;"
                    >
                        <div class="px-3 py-2">
                            <p class="truncate text-sm font-semibold text-white">{{ auth()->user()->name }}</p>
                            <p class="truncate text-xs text-white/50">{{ auth()->user()->email }}</p>
                        </div>
                        <div class="my-1 h-px bg-white/10"></div>
                        <a href="{{ route('profiles.index') }}" class="block rounded px-3 py-2 text-sm font-medium text-white/80 hover:bg-white/10 hover:text-gold">{{ __('site.header.profile') }}</a>
                        <a href="{{ route('profiles.edit') }}" class="block rounded px-3 py-2 text-sm font-medium text-white/80 hover:bg-white/10 hover:text-gold">{{ __('site.header.settings') }}</a>
                        <div class="my-1 h-px bg-white/10"></div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="block w-full rounded px-3 py-2 text-left text-sm font-medium text-red-400 hover:bg-red-500/10">{{ __('site.header.logout') }}</button>
                        </form>
                    </div>
                </div>

                {{-- Бургер (моб.) --}}
                <button
                    type="button"
                    @click="mobileOpen = !mobileOpen"
                    class="flex h-10 w-10 items-center justify-center rounded text-white/85 hover:bg-white/10 xl:hidden"
                    aria-label="{{ __('site.header.open_menu') }}"
                    :aria-expanded="mobileOpen"
                >
                    <svg x-show="!mobileOpen" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M4 7h16M4 12h16M4 17h16" /></svg>
                    <svg x-show="mobileOpen" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" style="display: none;"><path d="M6 6l12 12M18 6 6 18" /></svg>
                </button>
            </div>
        @else
            {{-- Гостевые кнопки --}}
            <div class="hidden items-center gap-2 sm:flex">
                <x-locale-switcher :locales="$locales" :current="$currentLocale" />

                {{-- Переключатель темы --}}
                <button
                    type="button"
                    id="themeToggle"
                    aria-label="{{ __('site.header.theme_toggle') }}"
                    class="flex h-10 w-10 items-center justify-center rounded text-white/85 transition hover:bg-white/10 hover:text-gold"
                >
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" class="hidden dark:block"><circle cx="12" cy="12" r="4" /><path d="M12 2v2M12 20v2M4.9 4.9l1.4 1.4M17.7 17.7l1.4 1.4M2 12h2M20 12h2M4.9 19.1l1.4-1.4M17.7 6.3l1.4-1.4" /></svg>
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" class="dark:hidden"><path d="M21 12.8A9 9 0 1 1 11.2 3a7 7 0 0 0 9.8 9.8Z" /></svg>
                </button>
                <a href="{{ route('billing.plans') }}" class="rounded px-4 py-2 text-sm font-semibold text-white transition hover:text-gold">{{ __('site.billing.nav') }}</a>
                <a href="#how-it-works" class="rounded px-4 py-2 text-sm font-semibold text-white transition hover:text-gold">{{ __('site.header.how_it_works') }}</a>
                <a href="{{ route('login') }}" class="rounded px-4 py-2 text-sm font-semibold text-white transition hover:text-gold">{{ __('site.header.login') }}</a>
                <a href="{{ route('register') }}" class="rounded border-2 border-gold px-5 py-2 text-xs font-bold uppercase tracking-wider text-gold transition hover:bg-gold hover:text-navy">
                    {{ __('site.header.start_free') }}
                </a>
            </div>

            <button
                type="button"
                @click="mobileOpen = !mobileOpen"
                class="flex h-10 w-10 items-center justify-center rounded text-white/85 hover:bg-white/10 sm:hidden"
                aria-label="{{ __('site.header.open_menu') }}"
                :aria-expanded="mobileOpen"
            >
                <svg x-show="!mobileOpen" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M4 7h16M4 12h16M4 17h16" /></svg>
                <svg x-show="mobileOpen" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" style="display: none;"><path d="M6 6l12 12M18 6 6 18" /></svg>
            </button>
        @endauth
    </nav>

    {{-- Мобильное меню --}}
    <div
        x-show="mobileOpen"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 -translate-y-2"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 -translate-y-2"
        class="border-t border-white/10 bg-navy2 xl:hidden"
        style="display: none;"
    >
        <div class="space-y-1 px-4 py-3">
            @auth
                @foreach ($navItems as $item)
                    <a href="{{ $item['url'] }}" class="block rounded px-3 py-2.5 text-sm font-semibold {{ $item['active'] ? 'text-gold' : 'text-white/80 hover:bg-white/10' }}">{{ $item['label'] }}</a>
                @endforeach
                <div class="my-2 h-px bg-white/10"></div>
                <a href="{{ route('notifications.index') }}" class="flex items-center justify-between rounded px-3 py-2.5 text-sm font-semibold text-white/80 hover:bg-white/10">
                    {{ __('site.header.notifications') }}
                    @if ($unreadCount > 0)
                        <span class="flex h-5 min-w-5 items-center justify-center rounded-full bg-gold px-1 text-[11px] font-extrabold text-navy">{{ $unreadCount > 9 ? '9+' : $unreadCount }}</span>
                    @endif
                </a>
                <a href="{{ route('profiles.index') }}" class="block rounded px-3 py-2.5 text-sm font-semibold text-white/80 hover:bg-white/10">{{ __('site.header.profile') }}</a>
                <a href="{{ route('profiles.edit') }}" class="block rounded px-3 py-2.5 text-sm font-semibold text-white/80 hover:bg-white/10">{{ __('site.header.settings') }}</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="block w-full rounded px-3 py-2.5 text-left text-sm font-semibold text-red-400 hover:bg-red-500/10">{{ __('site.header.logout') }}</button>
                </form>
            @else
                <a href="#how-it-works" class="block rounded px-3 py-2.5 text-sm font-semibold text-white/80 hover:bg-white/10">{{ __('site.header.how_it_works') }}</a>
                <a href="{{ route('login') }}" class="block rounded px-3 py-2.5 text-sm font-semibold text-white/80 hover:bg-white/10">{{ __('site.header.login') }}</a>
                <a href="{{ route('register') }}" class="block rounded border-2 border-gold px-3 py-2.5 text-center text-sm font-bold uppercase tracking-wide text-gold">{{ __('site.header.start_free') }}</a>
            @endauth
        </div>
    </div>
</header>
