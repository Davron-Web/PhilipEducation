<!DOCTYPE html>
<html lang="ru" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('code') — Philip Education</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('vendor/bootstrap/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/bootstrap-icons/bootstrap-icons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/philip-theme.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/philip-neon.css') }}">
</head>
<body>

<div class="ph-neon-bg" aria-hidden="true">
    <div class="ph-neon-grid"></div>
    <div class="ph-neon-orb ph-neon-orb-a"></div>
    <div class="ph-neon-orb ph-neon-orb-b"></div>
    <div class="ph-neon-orb ph-neon-orb-c"></div>
</div>

<div class="d-flex flex-column align-items-center justify-content-center text-center min-vh-100 px-3">
    <span class="d-inline-flex align-items-center justify-content-center rounded-4 text-white fw-bold ph-logo-mark mb-4"
          style="width: 4.5rem; height: 4.5rem; background: linear-gradient(135deg, #2563EB, #1d4ed8);">
        <svg width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 6l2 3"/><path d="M18 6l-2 3"/><ellipse cx="12" cy="13" rx="7" ry="8"/><circle cx="9" cy="12" r="2.2"/><circle cx="15" cy="12" r="2.2"/><circle cx="9" cy="12" r=".4" fill="#fff" stroke="none"/><circle cx="15" cy="12" r=".4" fill="#fff" stroke="none"/><path d="M11.3 14.5h1.4l-.7 1.2z" fill="#fff" stroke="none"/></svg>
    </span>

    <div class="display-1 fw-bold ph-neon-grad-text mb-2">@yield('code')</div>
    <h1 class="h3 fw-bold mb-2">@yield('title')</h1>
    <p class="text-secondary mb-4" style="max-width: 32rem;">@yield('message')</p>

    <div class="d-flex gap-2 flex-wrap justify-content-center">
        <a href="{{ url('/') }}" class="btn btn-accent fw-semibold">
            <i class="bi bi-house-door me-1"></i>На главную
        </a>
        <a href="javascript:history.back()" class="btn btn-outline-light">
            <i class="bi bi-arrow-left me-1"></i>Назад
        </a>
    </div>
</div>

</body>
</html>
