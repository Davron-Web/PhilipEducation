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

        /* Звёздное небо — тайл со звёздами, тайлится по всему фону */
        .bg-starfield {
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='300' height='300' viewBox='0 0 300 300'%3E%3Cg fill='%23E8F6FB'%3E%3Ccircle cx='12' cy='40' r='0.8' fill-opacity='.7'/%3E%3Ccircle cx='55' cy='15' r='0.6' fill-opacity='.6'/%3E%3Ccircle cx='90' cy='70' r='1.3' fill-opacity='.8'/%3E%3Ccircle cx='130' cy='30' r='0.7' fill-opacity='.6'/%3E%3Ccircle cx='210' cy='20' r='0.6' fill-opacity='.6'/%3E%3Ccircle cx='250' cy='55' r='1.5' fill-opacity='.9'/%3E%3Ccircle cx='20' cy='120' r='1.1' fill-opacity='.7'/%3E%3Ccircle cx='60' cy='160' r='0.7' fill-opacity='.6'/%3E%3Ccircle cx='140' cy='180' r='0.6' fill-opacity='.5'/%3E%3Ccircle cx='180' cy='150' r='0.9' fill-opacity='.7'/%3E%3Ccircle cx='260' cy='200' r='1.2' fill-opacity='.8'/%3E%3Ccircle cx='30' cy='220' r='0.7' fill-opacity='.6'/%3E%3Ccircle cx='110' cy='230' r='0.6' fill-opacity='.5'/%3E%3Ccircle cx='150' cy='260' r='1.4' fill-opacity='.85'/%3E%3Ccircle cx='230' cy='280' r='0.7' fill-opacity='.6'/%3E%3Ccircle cx='270' cy='250' r='1.1' fill-opacity='.7'/%3E%3Ccircle cx='50' cy='90' r='0.9' fill-opacity='.7'/%3E%3Ccircle cx='95' cy='10' r='0.5' fill-opacity='.5'/%3E%3Ccircle cx='240' cy='130' r='0.6' fill-opacity='.6'/%3E%3Ccircle cx='5' cy='180' r='0.5' fill-opacity='.5'/%3E%3C/g%3E%3Cg fill='%237DD3FC'%3E%3Ccircle cx='170' cy='90' r='1.0' fill-opacity='.8'/%3E%3Ccircle cx='280' cy='110' r='0.8' fill-opacity='.7'/%3E%3Ccircle cx='100' cy='140' r='1.6' fill-opacity='.9'/%3E%3Ccircle cx='220' cy='170' r='0.8' fill-opacity='.7'/%3E%3Ccircle cx='70' cy='250' r='1.0' fill-opacity='.8'/%3E%3Ccircle cx='190' cy='240' r='0.8' fill-opacity='.7'/%3E%3Ccircle cx='10' cy='280' r='0.6' fill-opacity='.6'/%3E%3Ccircle cx='160' cy='110' r='0.7' fill-opacity='.7'/%3E%3Ccircle cx='290' cy='20' r='0.8' fill-opacity='.7'/%3E%3Ccircle cx='200' cy='290' r='1.0' fill-opacity='.8'/%3E%3C/g%3E%3C/svg%3E");
            background-size: 300px 300px;
        }

        @keyframes star-twinkle {
            0%, 100% { opacity: .55; }
            50% { opacity: 1; }
        }
        .animate-twinkle { animation: star-twinkle 5s ease-in-out infinite; }

        /* Уважаем предпочтение отключить анимации */
        @media (prefers-reduced-motion: reduce) {
            [data-reveal] { opacity: 1 !important; transform: none !important; transition: none !important; }
            .animate-blob, .animate-blob-slow, .animate-blob-delay, .animate-twinkle { animation: none !important; }
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
        <div class="absolute inset-0 bg-starfield animate-twinkle opacity-80"></div>

        {{-- Яркие крупные звёзды со свечением --}}
        <div class="absolute left-[15%] top-[20%] h-1.5 w-1.5 rounded-full bg-white shadow-[0_0_12px_4px_rgba(255,255,255,.8)]"></div>
        <div class="absolute left-[80%] top-[15%] h-1 w-1 rounded-full bg-skylight shadow-[0_0_10px_3px_rgba(125,211,252,.9)]"></div>
        <div class="absolute left-[70%] top-[65%] h-2 w-2 rounded-full bg-sun shadow-[0_0_16px_5px_rgba(250,204,21,.7)]"></div>
        <div class="absolute left-[10%] top-[75%] h-1 w-1 rounded-full bg-white shadow-[0_0_10px_3px_rgba(255,255,255,.8)]"></div>
        <div class="absolute left-[45%] top-[40%] h-1 w-1 rounded-full bg-skylight shadow-[0_0_10px_3px_rgba(125,211,252,.9)]"></div>
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
