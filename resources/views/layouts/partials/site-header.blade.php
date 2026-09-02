{{-- Единая шапка сайта: логотип, навигация, поиск, streak и меню профиля.
     Для гостя показывает маркетинговые кнопки, для авторизованного — полное меню. --}}
@php
    $navItems = [
        ['label' => 'Уроки', 'url' => route('lessons.index'), 'active' => request()->routeIs('lessons.*')],
        ['label' => 'Грамматика', 'url' => route('grammartopics.index'), 'active' => request()->routeIs('grammartopics.*')],
        ['label' => 'Словарь', 'url' => route('words.index'), 'active' => request()->routeIs('words.*')],
        ['label' => 'Выражения', 'url' => route('expressions.index'), 'active' => request()->routeIs('expressions.*')],
        ['label' => 'Упражнения', 'url' => route('exercises.index'), 'active' => request()->routeIs('exercises.*')],
        ['label' => 'Тесты', 'url' => route('tests.index'), 'active' => request()->routeIs('tests.*')],
        ['label' => 'IELTS', 'url' => route('ielts.index'), 'active' => request()->routeIs('ielts.*')],
        ['label' => 'Книги', 'url' => route('books.index'), 'active' => request()->routeIs('books.*')],
        ['label' => 'Достижения', 'url' => route('achievements.index'), 'active' => request()->routeIs('achievements.*')],
    ];

    $unreadCount = auth()->check()
        ? auth()->user()->notifications()->where('is_read', false)->count()
        : 0;
@endphp

<header
    x-data="{ mobileOpen: false, userMenuOpen: false, searchOpen: false }"
    @keydown.escape.window="mobileOpen = false; userMenuOpen = false; searchOpen = false"
    class="sticky top-0 z-50 border-b border-white/10 bg-armor2/70 backdrop-blur-xl"
>
    <nav class="mx-auto flex h-16 max-w-7xl items-center justify-between gap-3 px-4 sm:px-6 lg:px-8" aria-label="Основная навигация">
        {{-- Логотип --}}
        <a href="{{ auth()->check() ? route('user.dashboard') : url('/') }}" class="flex shrink-0 items-center gap-2 group">
            <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-gradient-to-br from-[#FFE75E] via-sun to-[#E0B400] shadow-[0_0_14px_rgba(255,215,0,.5)] transition-transform duration-300 group-hover:-rotate-6">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#171325" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M6 6l2 3" /><path d="M18 6l-2 3" />
                    <ellipse cx="12" cy="13" rx="7" ry="8" />
                    <circle cx="9" cy="12" r="1.4" fill="#171325" stroke="none" />
                    <circle cx="15" cy="12" r="1.4" fill="#171325" stroke="none" />
                </svg>
            </span>
            <span class="hidden font-display text-base font-bold bg-gradient-to-r from-ink to-sky bg-clip-text text-transparent sm:block">
                Philip Education
            </span>
        </a>

        @auth
            {{-- Десктоп-навигация (авторизован) --}}
            <div class="hidden items-center gap-0.5 xl:flex">
                @foreach ($navItems as $item)
                    <a
                        href="{{ $item['url'] }}"
                        class="whitespace-nowrap rounded-lg px-2.5 py-2 text-sm font-semibold transition {{ $item['active'] ? 'bg-sky/10 text-sky' : 'text-ink/70 hover:bg-white/5 hover:text-sky' }}"
                    >{{ $item['label'] }}</a>
                @endforeach
            </div>

            <div class="flex items-center gap-2">
                {{-- Поиск --}}
                <div class="relative hidden sm:block">
                    <button
                        type="button"
                        @click="searchOpen = !searchOpen"
                        class="flex h-10 w-10 items-center justify-center rounded-full text-ink/60 transition hover:bg-white/5 hover:text-sky"
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
                        class="absolute right-0 top-12 w-72 rounded-2xl border border-white/10 bg-armor2/90 p-2 shadow-xl shadow-ink/10 backdrop-blur-xl"
                        style="display: none;"
                    >
                        {{-- Поиск пока без обработчика — раздел поиска ещё не реализован --}}
                        <input
                            type="search"
                            placeholder="Искать уроки, слова..."
                            class="w-full rounded-xl border border-white/10 bg-armor px-3 py-2 text-sm text-ink placeholder:text-ink/40 focus:border-sky focus:outline-none focus:ring-2 focus:ring-sky/20"
                        >
                    </div>
                </div>

                {{-- Уведомления --}}
                <a
                    href="{{ route('notifications.index') }}"
                    class="relative flex h-10 w-10 items-center justify-center rounded-full text-ink/60 transition hover:bg-white/5 hover:text-sky"
                    aria-label="Уведомления{{ $unreadCount > 0 ? " ({$unreadCount} непрочитано)" : '' }}"
                >
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9" /><path d="M10.3 21a1.94 1.94 0 0 0 3.4 0" /></svg>
                    @if ($unreadCount > 0)
                        <span class="absolute right-1.5 top-1.5 flex h-4 min-w-4 items-center justify-center rounded-full bg-sun px-1 text-[10px] font-extrabold text-[#171325]">
                            {{ $unreadCount > 9 ? '9+' : $unreadCount }}
                        </span>
                    @endif
                </a>

                {{-- Streak: показываем только если контроллер прислал значение --}}
                @isset($streak)
                    <div class="hidden items-center gap-1 rounded-full bg-sun/10 border border-sun/30 px-3 py-1.5 text-sm font-bold text-sun sm:flex" title="Серия дней подряд">
                        <span aria-hidden="true">🔥</span>{{ $streak }}
                    </div>
                @endisset

                {{-- Меню профиля --}}
                <div class="relative">
                    <button
                        type="button"
                        @click="userMenuOpen = !userMenuOpen"
                        class="flex items-center gap-2 rounded-full border border-white/10 bg-armor2/70 py-1 pl-1 pr-2 transition hover:border-sky/40 hover:bg-armor2"
                    >
                        <span class="flex h-8 w-8 items-center justify-center rounded-full bg-gradient-to-br from-brand to-sky text-sm font-bold text-ink">
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
                        class="absolute right-0 top-12 w-56 origin-top-right overflow-hidden rounded-2xl border border-white/10 bg-armor2/90 p-1.5 shadow-xl shadow-ink/10 backdrop-blur-xl"
                        style="display: none;"
                    >
                        <div class="px-3 py-2">
                            <p class="truncate text-sm font-semibold text-ink">{{ auth()->user()->name }}</p>
                            <p class="truncate text-xs text-ink/50">{{ auth()->user()->email }}</p>
                        </div>
                        <div class="my-1 h-px bg-ink/10"></div>
                        <a href="{{ route('profiles.index') }}" class="block rounded-xl px-3 py-2 text-sm font-medium text-ink/80 hover:bg-white/5 hover:text-sky">Профиль</a>
                        <a href="{{ route('profiles.edit') }}" class="block rounded-xl px-3 py-2 text-sm font-medium text-ink/80 hover:bg-white/5 hover:text-sky">Настройки</a>
                        <div class="my-1 h-px bg-ink/10"></div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="block w-full rounded-xl px-3 py-2 text-left text-sm font-medium text-red-400 hover:bg-red-500/10">Выйти</button>
                        </form>
                    </div>
                </div>

                {{-- Бургер (моб.) --}}
                <button
                    type="button"
                    @click="mobileOpen = !mobileOpen"
                    class="flex h-10 w-10 items-center justify-center rounded-full text-ink/70 hover:bg-white/5 xl:hidden"
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
                <a href="#how-it-works" class="rounded-lg px-4 py-2 text-sm font-semibold text-ink/70 transition hover:text-sky">Как это работает</a>
                <a href="{{ route('login') }}" class="rounded-lg px-4 py-2 text-sm font-semibold text-ink/70 transition hover:text-sky">Войти</a>
                <a href="{{ route('register') }}" class="rounded-xl bg-gradient-to-b from-[#FFE75E] via-sun to-[#E0B400] px-5 py-2.5 text-sm font-bold text-[#171325] shadow-lg shadow-brand/25 transition hover:-translate-y-0.5 hover:shadow-xl">
                    Начать бесплатно
                </a>
            </div>

            <button
                type="button"
                @click="mobileOpen = !mobileOpen"
                class="flex h-10 w-10 items-center justify-center rounded-full text-ink/70 hover:bg-white/5 sm:hidden"
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
        class="border-t border-white/10 bg-armor2/95 backdrop-blur-xl xl:hidden"
        style="display: none;"
    >
        <div class="space-y-1 px-4 py-3">
            @auth
                @foreach ($navItems as $item)
                    <a href="{{ $item['url'] }}" class="block rounded-xl px-3 py-2.5 text-sm font-semibold {{ $item['active'] ? 'bg-sky/10 text-sky' : 'text-ink/80 hover:bg-white/5' }}">{{ $item['label'] }}</a>
                @endforeach
                <div class="my-2 h-px bg-ink/10"></div>
                <a href="{{ route('notifications.index') }}" class="flex items-center justify-between rounded-xl px-3 py-2.5 text-sm font-semibold text-ink/80 hover:bg-white/5">
                    Уведомления
                    @if ($unreadCount > 0)
                        <span class="flex h-5 min-w-5 items-center justify-center rounded-full bg-sun px-1 text-[11px] font-extrabold text-[#171325]">{{ $unreadCount > 9 ? '9+' : $unreadCount }}</span>
                    @endif
                </a>
                <a href="{{ route('profiles.index') }}" class="block rounded-xl px-3 py-2.5 text-sm font-semibold text-ink/80 hover:bg-white/5">Профиль</a>
                <a href="{{ route('profiles.edit') }}" class="block rounded-xl px-3 py-2.5 text-sm font-semibold text-ink/80 hover:bg-white/5">Настройки</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="block w-full rounded-xl px-3 py-2.5 text-left text-sm font-semibold text-red-400 hover:bg-red-500/10">Выйти</button>
                </form>
            @else
                <a href="#how-it-works" class="block rounded-xl px-3 py-2.5 text-sm font-semibold text-ink/80 hover:bg-white/5">Как это работает</a>
                <a href="{{ route('login') }}" class="block rounded-xl px-3 py-2.5 text-sm font-semibold text-ink/80 hover:bg-white/5">Войти</a>
                <a href="{{ route('register') }}" class="block rounded-xl bg-gradient-to-b from-[#FFE75E] via-sun to-[#E0B400] px-3 py-2.5 text-center text-sm font-bold text-[#171325]">Начать бесплатно</a>
            @endauth
        </div>
    </div>
</header>
