<header class="app-topbar sticky-top">
    <div class="d-flex align-items-center justify-content-between px-3 px-lg-4 py-3 gap-3">
        <div class="d-flex align-items-center gap-2 gap-lg-3 flex-grow-1">
            <button class="btn btn-outline-secondary d-lg-none" type="button"
                    data-bs-toggle="offcanvas" data-bs-target="#sidebarOffcanvas" aria-label="Открыть меню">
                <i class="bi bi-list fs-5"></i>
            </button>

            <div>
                <h1 class="h5 mb-0 fw-bold">@yield('page_title', 'Dashboard')</h1>
                <p class="d-none d-sm-block text-secondary small mb-0">@yield('page_description', 'Welcome back!')</p>
            </div>
        </div>

        <form role="search" class="d-none d-md-block flex-grow-1" style="max-width: 320px;" onsubmit="return false">
            <div class="input-group">
                <span class="input-group-text bg-body border-end-0"><i class="bi bi-search text-secondary"></i></span>
                <input type="search" class="form-control border-start-0" placeholder="Search lessons, words, tests…">
            </div>
        </form>

        <div class="d-flex align-items-center gap-2">
            <button id="theme-toggle" type="button" class="btn btn-outline-secondary" aria-label="Переключить тему">
                <i class="bi bi-moon-stars" data-theme-icon="dark"></i>
                <i class="bi bi-sun" data-theme-icon="light"></i>
            </button>

            <div class="dropdown">
                <button class="btn btn-outline-secondary position-relative" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-bell fs-6"></i>
                    @if(($unreadCount ?? 0) > 0)
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill text-bg-primary">
                            {{ $unreadCount }}
                            <span class="visually-hidden">unread notifications</span>
                        </span>
                    @endif
                </button>
                <div class="dropdown-menu dropdown-menu-end shadow-sm p-2" style="min-width: 320px;">
                    <div class="d-flex align-items-center justify-content-between px-2 pb-2">
                        <span class="fw-semibold">Notifications</span>
                        <a href="{{ route('notifications.index') }}" class="small link-primary text-decoration-none">View all</a>
                    </div>
                    @forelse(($recentNotifications ?? collect()) as $notification)
                        <a href="{{ route('notifications.show', $notification->id) }}"
                           class="dropdown-item rounded-3 py-2 {{ $notification->is_read ? '' : 'bg-primary-subtle' }}">
                            <div class="fw-semibold small text-truncate">{{ $notification->title }}</div>
                            <div class="text-secondary small text-truncate">{{ $notification->message }}</div>
                        </a>
                    @empty
                        <p class="text-secondary small px-2 py-3 mb-0 text-center">No notifications yet</p>
                    @endforelse
                </div>
            </div>

            @auth
                <div class="dropdown">
                    <button class="btn btn-light d-flex align-items-center gap-2 border" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <span class="avatar-circle" style="width: 2rem; height: 2rem; font-size: .8rem;">
                            {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
                        </span>
                        <span class="d-none d-lg-inline small fw-semibold">{{ auth()->user()->name }}</span>
                        <i class="bi bi-chevron-down small d-none d-lg-inline"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                        <li><a class="dropdown-item" href="{{ route('profiles.index') }}"><i class="bi bi-person-circle me-2"></i>Profile</a></li>
                        <li><a class="dropdown-item" href="{{ route('profiles.edit') }}"><i class="bi bi-gear me-2"></i>Settings</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger"><i class="bi bi-box-arrow-right me-2"></i>Logout</button>
                            </form>
                        </li>
                    </ul>
                </div>
            @endauth
        </div>
    </div>
</header>
