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
        <x-owl-mark :size="34" />
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
