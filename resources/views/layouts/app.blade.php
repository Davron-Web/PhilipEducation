<!DOCTYPE html>
<html lang="ru" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Philip Education') — @yield('page_title', 'Учите английский язык')</title>
    <meta name="description" content="@yield('meta_description', 'Philip Education — платформа для изучения английского языка: уроки, грамматика, словарь, упражнения и тесты.')">
    <meta property="og:title" content="@yield('title', 'Philip Education')">
    <meta property="og:description" content="@yield('meta_description', 'Philip Education — платформа для изучения английского языка: уроки, грамматика, словарь, упражнения и тесты.')">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="Philip Education">

    {{-- Шрифты: Unbounded для заголовков (font-display), Manrope для текста --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Unbounded:wght@500;700;800&family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    {{--
        Сборки через npm на этой машине нет, поэтому Tailwind и Alpine
        подключены через CDN (без шага компиляции). Палитра — тема "Lingua
        Arcana" (тёмное стекло + фиолетовый/циан-неон/золото), перенесённая
        с гостевой главной на весь сайт. Держите в синхроне с tailwind.config.js.
    --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Manrope', 'ui-sans-serif', 'system-ui', 'sans-serif'],
                        display: ['Unbounded', 'ui-sans-serif', 'system-ui', 'sans-serif'],
                    },
                    colors: {
                        ink: '#EAF0F6',
                        armor: '#1E222A',
                        armor2: '#252B36',
                        brand: '#7C3AED',
                        sky: '#22D3EE',
                        skylight: '#5EEAD4',
                        sun: '#FFD700',
                    },
                },
            },
        };
    </script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script src="{{ asset('assets/js/pronounce.js') }}"></script>
    <script src="{{ asset('assets/js/flashcard-deck.js') }}"></script>

    <style>
        /* Плавное появление элементов при скролле (см. IntersectionObserver внизу файла) */
        [data-reveal] {
            opacity: 0;
            transform: translateY(24px);
            transition: opacity .5s ease-out, transform .5s ease-out;
        }
        [data-reveal].is-visible {
            opacity: 1;
            transform: translateY(0);
        }

        /* Уважаем предпочтение отключить анимации */
        @media (prefers-reduced-motion: reduce) {
            [data-reveal] { opacity: 1 !important; transform: none !important; transition: none !important; }
            * { scroll-behavior: auto !important; }
        }
    </style>

    @stack('styles')
</head>
<body class="min-h-screen bg-armor font-sans text-ink antialiased">

    {{-- ===================== АНИМИРОВАННЫЙ ФОН (тот же, что на гостевой главной) ===================== --}}
    <div class="pointer-events-none fixed inset-0 -z-10 overflow-hidden bg-armor" aria-hidden="true">
        <div class="absolute inset-0" style="background:
            radial-gradient(700px 420px at 12% 8%, rgba(93,43,125,.35), transparent 60%),
            radial-gradient(640px 420px at 88% 30%, rgba(0,229,255,.12), transparent 60%),
            radial-gradient(520px 380px at 50% 100%, rgba(93,43,125,.22), transparent 65%);"></div>
        <canvas id="bgParticleCanvas" class="absolute inset-0"></canvas>
    </div>

    @include('layouts.partials.site-header')

    <main class="relative">
        @if (session('success'))
            <div class="mx-auto mt-6 max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="flex items-center gap-2 rounded-2xl border border-green-200 bg-green-50 px-4 py-3 text-sm font-medium text-green-800">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="shrink-0"><path d="M20 6 9 17l-5-5" /></svg>
                    {{ session('success') }}
                </div>
            </div>
        @endif

        @if (session('error'))
            <div class="mx-auto mt-6 max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="flex items-center gap-2 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-800">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="shrink-0"><circle cx="12" cy="12" r="10" /><path d="M12 8v5M12 16h.01" /></svg>
                    {{ session('error') }}
                </div>
            </div>
        @endif

        @if (session('info'))
            <div class="mx-auto mt-6 max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="flex items-center gap-2 rounded-2xl border border-brand/20 bg-brand/5 px-4 py-3 text-sm font-medium text-ink">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="shrink-0"><circle cx="12" cy="12" r="10" /><path d="M12 16v-4M12 8h.01" /></svg>
                    {{ session('info') }}
                </div>
            </div>
        @endif

        @yield('content')
    </main>

    @include('layouts.partials.site-footer')

    <x-phil-widget />

    <script>
        // Анимация нарастания числа — используется на статистике/дашборде:
        // x-data x-init="countUp($el, 0, 42, 900)"
        window.countUp = function (el, from, to, duration) {
            if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
                el.textContent = to;
                return;
            }
            var start = null;
            function step(ts) {
                if (!start) start = ts;
                var progress = Math.min((ts - start) / duration, 1);
                var eased = 1 - Math.pow(1 - progress, 3);
                el.textContent = Math.round(from + (to - from) * eased);
                if (progress < 1) requestAnimationFrame(step);
            }
            requestAnimationFrame(step);
        };

        // Проявление карточек и блоков с атрибутом data-reveal при попадании в область видимости
        document.addEventListener('DOMContentLoaded', function () {
            var prefersReduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
            var targets = document.querySelectorAll('[data-reveal]');
            if (prefersReduced || !('IntersectionObserver' in window)) {
                targets.forEach(function (el) { el.classList.add('is-visible'); });
                return;
            }

            var observer = new IntersectionObserver(function (entries) {
                entries.forEach(function (entry) {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('is-visible');
                        observer.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.12, rootMargin: '0px 0px -40px 0px' });

            targets.forEach(function (el) { observer.observe(el); });

            // Счётчики с анимацией нарастания: <span data-counter data-target="120">
            var counters = document.querySelectorAll('[data-counter]');
            if (counters.length) {
                if (prefersReduced || !('IntersectionObserver' in window)) {
                    counters.forEach(function (el) {
                        el.textContent = (parseInt(el.dataset.target, 10) || 0).toLocaleString('ru-RU');
                    });
                } else {
                    var counterObserver = new IntersectionObserver(function (entries) {
                        entries.forEach(function (entry) {
                            if (!entry.isIntersecting) return;
                            counterObserver.unobserve(entry.target);
                            var el = entry.target;
                            var target = parseInt(el.dataset.target, 10) || 0;
                            var duration = 1400;
                            var start = null;
                            function step(ts) {
                                if (!start) start = ts;
                                var progress = Math.min((ts - start) / duration, 1);
                                var eased = 1 - Math.pow(1 - progress, 3);
                                el.textContent = Math.round(eased * target).toLocaleString('ru-RU');
                                if (progress < 1) requestAnimationFrame(step);
                            }
                            requestAnimationFrame(step);
                        });
                    }, { threshold: 0.4 });
                    counters.forEach(function (el) { counterObserver.observe(el); });
                }
            }

            // Лёгкий параллакс декоративных элементов: <div data-parallax="0.2">
            var parallaxEls = document.querySelectorAll('[data-parallax]');
            if (parallaxEls.length && !prefersReduced) {
                window.addEventListener('scroll', function () {
                    var y = window.scrollY;
                    parallaxEls.forEach(function (el) {
                        var factor = parseFloat(el.dataset.parallax) || 0.2;
                        el.style.transform = 'translateY(' + (y * factor) + 'px)';
                    });
                }, { passive: true });
            }

            // Фоновые частицы (та же анимация, что на гостевой главной)
            var bgCanvas = document.getElementById('bgParticleCanvas');
            if (bgCanvas && !prefersReduced) {
                var bgCtx = bgCanvas.getContext('2d');
                var bgSparks = [];
                var bgSparkColors = ['0,255,255', '0,229,255', '77,238,234', '255,215,0', '138,75,184'];
                function bgSizeCanvas() { bgCanvas.width = bgCanvas.offsetWidth; bgCanvas.height = bgCanvas.offsetHeight; }
                function bgSeedSparks() {
                    bgSparks = [];
                    var amount = Math.min(70, Math.floor(bgCanvas.width / 22));
                    for (var i = 0; i < amount; i++) {
                        bgSparks.push({
                            posX: Math.random() * bgCanvas.width, posY: Math.random() * bgCanvas.height,
                            rad: Math.random() * 1.8 + .5, velY: -(Math.random() * .35 + .08), velX: (Math.random() - .5) * .25,
                            col: bgSparkColors[Math.floor(Math.random() * bgSparkColors.length)], phase: Math.random() * Math.PI * 2
                        });
                    }
                }
                function bgDrawSparks(t) {
                    bgCtx.clearRect(0, 0, bgCanvas.width, bgCanvas.height);
                    bgSparks.forEach(function (s) {
                        s.posY += s.velY; s.posX += s.velX;
                        if (s.posY < -6) { s.posY = bgCanvas.height + 6; s.posX = Math.random() * bgCanvas.width; }
                        if (s.posX < -6) s.posX = bgCanvas.width + 6;
                        if (s.posX > bgCanvas.width + 6) s.posX = -6;
                        var tw = .35 + Math.abs(Math.sin(t / 900 + s.phase)) * .65;
                        bgCtx.beginPath(); bgCtx.arc(s.posX, s.posY, s.rad, 0, Math.PI * 2);
                        bgCtx.fillStyle = 'rgba(' + s.col + ',' + tw.toFixed(2) + ')';
                        bgCtx.shadowColor = 'rgba(' + s.col + ',.9)'; bgCtx.shadowBlur = 8; bgCtx.fill();
                    });
                    requestAnimationFrame(bgDrawSparks);
                }
                bgSizeCanvas(); bgSeedSparks(); requestAnimationFrame(bgDrawSparks);
                window.addEventListener('resize', function () { bgSizeCanvas(); bgSeedSparks(); });
            }
        });
    </script>

    @stack('scripts')
</body>
</html>
