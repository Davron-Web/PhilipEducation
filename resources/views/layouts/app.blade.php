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

    {{-- Шрифт --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

    {{--
        Сборки через npm на этой машине нет, поэтому Tailwind и Alpine
        подключены через CDN (без шага компиляции). Палитра ниже продублирована
        в tailwind.config.js — держите оба места в синхроне при правках.
    --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'ui-sans-serif', 'system-ui', 'sans-serif'],
                    },
                    colors: {
                        ink: '#1E3A8A',
                        brand: '#2563EB',
                        sky: '#38BDF8',
                        skylight: '#7DD3FC',
                        sun: '#FACC15',
                    },
                },
            },
        };
    </script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script src="{{ asset('assets/js/pronounce.js') }}"></script>

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

        /* Дрейф размытых пятен на фоне */
        @keyframes blob-drift {
            0%, 100% { transform: translate(0, 0) scale(1); }
            33% { transform: translate(4%, -6%) scale(1.08); }
            66% { transform: translate(-3%, 4%) scale(0.96); }
        }
        .animate-blob { animation: blob-drift 18s ease-in-out infinite; }
        .animate-blob-slow { animation: blob-drift 24s ease-in-out infinite; }
        .animate-blob-delay { animation: blob-drift 20s ease-in-out infinite; animation-delay: -7s; }


        /* Уважаем предпочтение отключить анимации */
        @media (prefers-reduced-motion: reduce) {
            [data-reveal] { opacity: 1 !important; transform: none !important; transition: none !important; }
            .animate-blob, .animate-blob-slow, .animate-blob-delay { animation: none !important; }
            * { scroll-behavior: auto !important; }
        }
    </style>

    @stack('styles')
</head>
<body class="min-h-screen bg-white font-sans text-ink antialiased">

    {{-- ===================== АНИМИРОВАННЫЙ ФОН (тёмный, как в админке) ===================== --}}
    <div class="pointer-events-none fixed inset-0 -z-10 overflow-hidden bg-gradient-to-br from-[#05080F] via-[#0A192F] to-[#05080F]" aria-hidden="true">
        <div class="animate-blob absolute -top-24 -left-24 h-[28rem] w-[28rem] rounded-full bg-sky/25 blur-3xl"></div>
        <div class="animate-blob-slow absolute top-1/3 -right-32 h-[32rem] w-[32rem] rounded-full bg-skylight/20 blur-3xl"></div>
        <div class="animate-blob-delay absolute bottom-0 left-1/4 h-96 w-96 rounded-full bg-sky/15 blur-3xl"></div>
        <div class="animate-blob absolute -bottom-20 right-1/4 h-80 w-80 rounded-full bg-skylight/15 blur-3xl"></div>
        {{-- Толстые жёлтые ломаные "молнии" с тёмной обводкой — декоративный акцент фона --}}
        <svg class="absolute -left-6 top-10 h-[420px] w-[160px] -rotate-6 opacity-30" viewBox="0 0 140 500" fill="none" aria-hidden="true">
            <polyline points="55,0 48,150 18,240 2,500" stroke="#05080F" stroke-width="22" stroke-linejoin="miter" stroke-linecap="butt" />
            <polyline points="95,20 88,160 55,250 40,490" stroke="#05080F" stroke-width="22" stroke-linejoin="miter" stroke-linecap="butt" />
            <polyline points="55,0 48,150 18,240 2,500" stroke="#FACC15" stroke-width="15" stroke-linejoin="miter" stroke-linecap="butt" />
            <polyline points="95,20 88,160 55,250 40,490" stroke="#FACC15" stroke-width="15" stroke-linejoin="miter" stroke-linecap="butt" />
        </svg>
        <svg class="absolute -right-10 bottom-0 h-[380px] w-[150px] rotate-12 opacity-20" viewBox="0 0 140 500" fill="none" aria-hidden="true">
            <polyline points="55,0 48,150 18,240 2,500" stroke="#05080F" stroke-width="22" stroke-linejoin="miter" stroke-linecap="butt" />
            <polyline points="95,20 88,160 55,250 40,490" stroke="#05080F" stroke-width="22" stroke-linejoin="miter" stroke-linecap="butt" />
            <polyline points="55,0 48,150 18,240 2,500" stroke="#FACC15" stroke-width="15" stroke-linejoin="miter" stroke-linecap="butt" />
            <polyline points="95,20 88,160 55,250 40,490" stroke="#FACC15" stroke-width="15" stroke-linejoin="miter" stroke-linecap="butt" />
        </svg>
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
        });
    </script>

    @stack('scripts')
</body>
</html>
