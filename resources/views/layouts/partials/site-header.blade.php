{{-- Единая шапка сайта: логотип, навигация, поиск, streak и меню профиля.
     Для гостя показывает маркетинговые кнопки, для авторизованного — полное меню. --}}
@php
    $navItems = [
        ['label' => 'Уроки', 'url' => route('lessons.index'), 'active' => request()->routeIs('lessons.*')],
        ['label' => 'Словарь', 'url' => route('words.index'), 'active' => request()->routeIs('words.*')],
        ['label' => 'Грамматика', 'url' => route('grammartopics.index'), 'active' => request()->routeIs('grammartopics.*')],
        ['label' => 'Тесты', 'url' => route('tests.index'), 'active' => request()->routeIs('tests.*')],
        ['label' => 'Книги', 'url' => route('books.index'), 'active' => request()->routeIs('books.*')],
    ];
@endphp

<header
    x-data="{ mobileOpen: false, userMenuOpen: false, searchOpen: false }"
    @keydown.escape.window="mobileOpen = false; userMenuOpen = false; searchOpen = false"
    class="sticky top-0 z-50 border-b border-white/50 bg-white/70 backdrop-blur-xl"
>
    <nav class="mx-auto flex h-16 max-w-7xl items-center justify-between gap-3 px-4 sm:px-6 lg:px-8" aria-label="Основная навигация">
        {{-- Логотип --}}
        <a href="{{ auth()->check() ? route('user.dashboard') : url('/') }}" class="flex shrink-0 items-center gap-2 group">
            <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-gradient-to-br from-brand to-sky text-white shadow-lg shadow-brand/30 transition-transform duration-300 group-hover:-rotate-6">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M6 6l2 3" /><path d="M18 6l-2 3" />
                    <ellipse cx="12" cy="13" rx="7" ry="8" />
                    <circle cx="9" cy="12" r="1.4" fill="currentColor" stroke="none" />
                    <circle cx="15" cy="12" r="1.4" fill="currentColor" stroke="none" />
                </svg>
            </span>
            <span class="hidden text-lg font-extrabold bg-gradient-to-r from-ink to-sky bg-clip-text text-transparent sm:block">
                Philip Education
            </span>
        </a>

        @auth
            {{-- Десктоп-навигация (авторизован) --}}
            <div class="hidden items-center gap-1 lg:flex">
                @foreach ($navItems as $item)
                    <a
                        href="{{ $item['url'] }}"
                        class="rounded-lg px-3 py-2 text-sm font-semibold transition {{ $item['active'] ? 'bg-brand/10 text-brand' : 'text-ink/70 hover:bg-brand/5 hover:text-brand' }}"
                    >{{ $item['label'] }}</a>
                @endforeach
            </div>

            <div class="flex items-center gap-2">
                {{-- Поиск --}}
                <div class="relative hidden sm:block">
                    <button
                        type="button"
                        @click="searchOpen = !searchOpen"
                        class="flex h-10 w-10 items-center justify-center rounded-full text-ink/60 transition hover:bg-brand/5 hover:text-brand"
                        aria-label="Поиск"
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
                        class="absolute right-0 top-12 w-72 rounded-2xl border border-white/60 bg-white/90 p-2 shadow-xl shadow-ink/10 backdrop-blur-xl"
                        style="display: none;"
                    >
                        {{-- Поиск пока без обработчика — раздел поиска ещё не реализован --}}
                        <input
                            type="search"
                            placeholder="Искать уроки, слова..."
                            class="w-full rounded-xl border border-ink/10 bg-white px-3 py-2 text-sm text-ink placeholder:text-ink/40 focus:border-brand focus:outline-none focus:ring-2 focus:ring-brand/20"
                        >
                    </div>
                </div>

                {{-- Streak: показываем только если контроллер прислал значение --}}
                @isset($streak)
                    <div class="hidden items-center gap-1 rounded-full bg-sun/15 px-3 py-1.5 text-sm font-bold text-amber-700 sm:flex" title="Серия дней подряд">
                        <span aria-hidden="true">🔥</span>{{ $streak }}
                    </div>
                @endisset

                {{-- Меню профиля --}}
                <div class="relative">
                    <button
                        type="button"
                        @click="userMenuOpen = !userMenuOpen"
                        class="flex items-center gap-2 rounded-full border border-white/60 bg-white/60 py-1 pl-1 pr-2 transition hover:border-brand/30 hover:bg-white"
                    >
                        <span class="flex h-8 w-8 items-center justify-center rounded-full bg-gradient-to-br from-brand to-sky text-sm font-bold text-white">
                            {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
                        </span>
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" class="hidden text-ink/50 sm:block"><path d="m6 9 6 6 6-6" /></svg>
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
                        class="absolute right-0 top-12 w-56 origin-top-right overflow-hidden rounded-2xl border border-white/60 bg-white/90 p-1.5 shadow-xl shadow-ink/10 backdrop-blur-xl"
                        style="display: none;"
                    >
                        <div class="px-3 py-2">
                            <p class="truncate text-sm font-semibold text-ink">{{ auth()->user()->name }}</p>
                            <p class="truncate text-xs text-ink/50">{{ auth()->user()->email }}</p>
                        </div>
                        <div class="my-1 h-px bg-ink/10"></div>
                        <a href="{{ route('profiles.index') }}" class="block rounded-xl px-3 py-2 text-sm font-medium text-ink/80 hover:bg-brand/5 hover:text-brand">Профиль</a>
                        <a href="{{ route('profiles.edit') }}" class="block rounded-xl px-3 py-2 text-sm font-medium text-ink/80 hover:bg-brand/5 hover:text-brand">Настройки</a>
                        <a href="{{ route('notifications.index') }}" class="block rounded-xl px-3 py-2 text-sm font-medium text-ink/80 hover:bg-brand/5 hover:text-brand">Уведомления</a>
                        <div class="my-1 h-px bg-ink/10"></div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="block w-full rounded-xl px-3 py-2 text-left text-sm font-medium text-red-600 hover:bg-red-50">Выйти</button>
                        </form>
                    </div>
                </div>

                {{-- Бургер (моб.) --}}
                <button
                    type="button"
                    @click="mobileOpen = !mobileOpen"
                    class="flex h-10 w-10 items-center justify-center rounded-full text-ink/70 hover:bg-brand/5 lg:hidden"
                    aria-label="Открыть меню"
                    :aria-expanded="mobileOpen"
                >
                    <svg x-show="!mobileOpen" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M4 7h16M4 12h16M4 17h16" /></svg>
                    <svg x-show="mobileOpen" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" style="display: none;"><path d="M6 6l12 12M18 6 6 18" /></svg>
                </button>
            </div>
        @else
            {{-- Гостевые кнопки --}}
            <div class="hidden items-center gap-2 sm:flex">
                <a href="#how-it-works" class="rounded-lg px-4 py-2 text-sm font-semibold text-ink/70 transition hover:text-brand">Как это работает</a>
                <a href="{{ route('login') }}" class="rounded-lg px-4 py-2 text-sm font-semibold text-ink/70 transition hover:text-brand">Войти</a>
                <a href="{{ route('register') }}" class="rounded-xl bg-gradient-to-r from-brand to-sky px-5 py-2.5 text-sm font-bold text-white shadow-lg shadow-brand/25 transition hover:-translate-y-0.5 hover:shadow-xl">
                    Начать бесплатно
                </a>
            </div>

            <button
                type="button"
                @click="mobileOpen = !mobileOpen"
                class="flex h-10 w-10 items-center justify-center rounded-full text-ink/70 hover:bg-brand/5 sm:hidden"
                aria-label="Открыть меню"
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
        class="border-t border-white/50 bg-white/95 backdrop-blur-xl lg:hidden"
        style="display: none;"
    >
        <div class="space-y-1 px-4 py-3">
            @auth
                @foreach ($navItems as $item)
                    <a href="{{ $item['url'] }}" class="block rounded-xl px-3 py-2.5 text-sm font-semibold {{ $item['active'] ? 'bg-brand/10 text-brand' : 'text-ink/80 hover:bg-brand/5' }}">{{ $item['label'] }}</a>
                @endforeach
                <div class="my-2 h-px bg-ink/10"></div>
                <a href="{{ route('profiles.index') }}" class="block rounded-xl px-3 py-2.5 text-sm font-semibold text-ink/80 hover:bg-brand/5">Профиль</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="block w-full rounded-xl px-3 py-2.5 text-left text-sm font-semibold text-red-600 hover:bg-red-50">Выйти</button>
                </form>
            @else
                <a href="#how-it-works" class="block rounded-xl px-3 py-2.5 text-sm font-semibold text-ink/80 hover:bg-brand/5">Как это работает</a>
                <a href="{{ route('login') }}" class="block rounded-xl px-3 py-2.5 text-sm font-semibold text-ink/80 hover:bg-brand/5">Войти</a>
                <a href="{{ route('register') }}" class="block rounded-xl bg-gradient-to-r from-brand to-sky px-3 py-2.5 text-center text-sm font-bold text-white">Начать бесплатно</a>
            @endauth
        </div>
    </div>
</header>
