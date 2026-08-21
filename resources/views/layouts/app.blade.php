<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'English Start') — @yield('page_title', 'Learning Platform')</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('vendor/bootstrap/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/bootstrap-icons/bootstrap-icons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/app.css') }}">

    @stack('styles')
</head>
<body>

@php
    $unreadCount = 0;
    $recentNotifications = collect();
    if (auth()->check()) {
        $unreadCount = auth()->user()->notifications()->where('is_read', false)->count();
        $recentNotifications = auth()->user()->notifications()->latest()->take(5)->get();
    }
@endphp

<div class="d-flex">

    {{-- ===================== DESKTOP SIDEBAR ===================== --}}
    <aside class="app-sidebar d-none d-lg-flex flex-column position-fixed">
        <div class="p-4 border-bottom">
            <a href="{{ route('user.dashboard') }}" class="d-flex align-items-center gap-2 text-decoration-none">
                <span class="d-inline-flex align-items-center justify-content-center rounded-3 text-white fw-bold"
                      style="width: 2.5rem; height: 2.5rem; background: linear-gradient(135deg, #2563EB, #1d4ed8);">
                    <i class="bi bi-mortarboard-fill"></i>
                </span>
                <span>
                    <span class="d-block fw-bold text-body">English Start</span>
                    <span class="d-block small text-secondary">Learning Platform</span>
                </span>
            </a>
        </div>

        <nav class="flex-grow-1 overflow-y-auto p-3">
            @include('partials.sidebar-nav')
        </nav>

        @auth
            <div class="p-3 border-top">
                <div class="d-flex align-items-center gap-2 p-2 rounded-3 bg-primary-subtle">
                    <span class="avatar-circle" style="width: 2.25rem; height: 2.25rem; font-size: .85rem;">
                        {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
                    </span>
                    <div class="flex-grow-1 text-truncate">
                        <div class="fw-semibold small text-truncate">{{ auth()->user()->name }}</div>
                        <div class="text-secondary text-truncate" style="font-size: .75rem;">{{ auth()->user()->email }}</div>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-link text-danger p-1" title="Logout">
                            <i class="bi bi-box-arrow-right"></i>
                        </button>
                    </form>
                </div>
            </div>
        @endauth
    </aside>

    {{-- ===================== MOBILE OFFCANVAS SIDEBAR ===================== --}}
    <div class="offcanvas offcanvas-start" tabindex="-1" id="sidebarOffcanvas">
        <div class="offcanvas-header border-bottom">
            <a href="{{ route('user.dashboard') }}" class="d-flex align-items-center gap-2 text-decoration-none">
                <span class="d-inline-flex align-items-center justify-content-center rounded-3 text-white fw-bold"
                      style="width: 2.25rem; height: 2.25rem; background: linear-gradient(135deg, #2563EB, #1d4ed8);">
                    <i class="bi bi-mortarboard-fill"></i>
                </span>
                <span class="fw-bold text-body">English Start</span>
            </a>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body p-3">
            @include('partials.sidebar-nav')
        </div>
    </div>

    {{-- ===================== MAIN ===================== --}}
    <div class="app-main flex-grow-1 min-vh-100 d-flex flex-column">
        @include('partials.topbar')

        <main class="flex-grow-1 p-3 p-lg-4">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible d-flex align-items-center gap-2" role="alert">
                    <i class="bi bi-check-circle-fill"></i>
                    <div>{{ session('success') }}</div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible d-flex align-items-center gap-2" role="alert">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                    <div>{{ session('error') }}</div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('info'))
                <div class="alert alert-primary alert-dismissible d-flex align-items-center gap-2" role="alert">
                    <i class="bi bi-info-circle-fill"></i>
                    <div>{{ session('info') }}</div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible" role="alert">
                    <ul class="mb-0 ps-3">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @yield('content')
        </main>

        @include('partials.footer')
    </div>
</div>

<script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('assets/js/app.js') }}"></script>
<script src="{{ asset('assets/js/pronounce.js') }}"></script>
@stack('scripts')

<x-phil-widget />
</body>
</html>
