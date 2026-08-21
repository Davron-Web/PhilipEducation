<!DOCTYPE html>
<html lang="ru" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') — English Academy</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('assets/css/admin.css') }}">

    @stack('styles')
</head>
<body class="admin-body">

<div class="layout">
    @include('layouts.partials.admin-sidebar')

    <div class="overlay" id="overlay"></div>

    <div class="main">
        @include('layouts.partials.admin-topbar')

        <main class="content">
            @if(session('success'))
                <div class="alert-card success">{{ session('success') }}</div>
            @endif

            @if(session('error'))
                <div class="alert-card error">{{ session('error') }}</div>
            @endif

            @if($errors->any())
                <div class="error-card">
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </main>
    </div>
</div>

<script src="{{ asset('assets/js/admin.js') }}"></script>
<script src="{{ asset('assets/js/pronounce.js') }}"></script>
@stack('scripts')

<x-phil-widget />
</body>
</html>
