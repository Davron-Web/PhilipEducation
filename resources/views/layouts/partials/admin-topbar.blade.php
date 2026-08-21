<header class="topbar">
    <button class="icon-btn burger" id="burger" aria-label="Меню">
        <svg width="20" height="20" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
    </button>

    <form action="{{ route('admin.search') }}" method="GET" class="search">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        <input type="text" name="q" placeholder="Поиск по системе..." value="{{ request('q') }}">
    </form>

    <div class="topbar-right">
        <a href="{{ route('admin.system.notifications.index') }}" class="icon-btn" aria-label="Уведомления">
            <svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
            <span class="dot"></span>
        </a>

        <div class="theme-wrap">☀️
            <label class="switch" title="Переключить тему"><input type="checkbox" id="themeToggle"><span class="slider"></span></label>
            🌙
        </div>

        <a href="{{ route('admin.profile.show') }}" class="profile">
            <span class="avatar">{{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}</span>
            <span>{{ auth()->user()->name ?? 'Администратор' }}</span>
        </a>
    </div>
</header>
