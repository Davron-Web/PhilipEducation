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

    {{-- Тема "Philip Bright": светлая по умолчанию, с переключателем на тёмную
         (класс .dark на <html>). Скрипт ниже применяет сохранённую/системную
         тему ДО отрисовки, чтобы не было мигания. --}}
    <script>
        (function () {
            var saved = null;
            try { saved = localStorage.getItem('pe-theme'); } catch (e) {}
            var prefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
            if (saved ? saved === 'dark' : prefersDark) {
                document.documentElement.classList.add('dark');
            }
        })();
    </script>

    {{-- Шрифты: Outfit для заголовков (font-display), Inter для текста --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    {{--
        Сборки через npm на этой машине нет, поэтому Tailwind и Alpine
        подключены через CDN (без шага компиляции). Палитра — тема "Philip
        Bright" (светлое стекло, индиго/бирюза/оранж, переключаемая тёмная
        тема через класс .dark). Цвета Tailwind ссылаются на CSS-переменные
        ниже, чтобы вся разметка сайта (bg-armor2, text-ink, text-sky...)
        автоматически подхватывала обе темы без правки каждой страницы.
    --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'ui-sans-serif', 'system-ui', 'sans-serif'],
                        display: ['Outfit', 'ui-sans-serif', 'system-ui', 'sans-serif'],
                    },
                    colors: {
                        ink: 'var(--pe-ink)',
                        armor: 'var(--pe-armor)',
                        armor2: 'var(--pe-armor2)',
                        surface2: 'var(--pe-surface2)',
                        line: 'var(--pe-line)',
                        brand: 'var(--pe-brand)',
                        sky: 'var(--pe-sky)',
                        skylight: 'var(--pe-skylight)',
                        sun: 'var(--pe-sun)',
                    },
                    borderRadius: {
                        '4xl': '24px',
                    },
                    boxShadow: {
                        soft: '0 10px 30px -12px rgba(79, 70, 229, .18)',
                        softLg: '0 20px 50px -20px rgba(79, 70, 229, .30)',
                    },
                },
            },
        };
    </script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script src="{{ asset('assets/js/pronounce.js') }}"></script>
    <script src="{{ asset('assets/js/flashcard-deck.js') }}"></script>

    <style>
        /* ---------- Цветовые токены: светлая тема (по умолчанию) ---------- */
        :root {
            --pe-ink: #0F172A;
            --pe-armor: #F8FAFC;
            --pe-armor2: #FFFFFF;
            --pe-surface2: #F1F5F9;
            --pe-line: #E2E8F0;
            --pe-brand: #4F46E5;
            --pe-sky: #06B6D4;
            --pe-skylight: #0891B2;
            --pe-sun: #F97316;
        }

        /* ---------- Тёмная тема: включается классом .dark на <html> ---------- */
        html.dark {
            --pe-ink: #E7E9F5;
            --pe-armor: #0B1020;
            --pe-armor2: #131A2E;
            --pe-surface2: #182036;
            --pe-line: #232B45;
            --pe-brand: #6366F1;
            --pe-sky: #22D3EE;
            --pe-skylight: #2DD4BF;
            --pe-sun: #FB923C;
        }

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

        /* Подъём карточки + смена тени при наведении */
        .card-lift {
            transition: transform .3s cubic-bezier(.22,1,.36,1), box-shadow .3s cubic-bezier(.22,1,.36,1), border-color .3s ease;
        }
        .card-lift:hover {
            transform: translateY(-6px);
            box-shadow: 0 20px 50px -20px rgba(79, 70, 229, .30);
        }

        body { transition: background-color .3s ease, color .3s ease; }

        /* Уважаем предпочтение отключить анимации */
        @media (prefers-reduced-motion: reduce) {
            [data-reveal] { opacity: 1 !important; transform: none !important; transition: none !important; }
            .card-lift { transition: none !important; }
            * { scroll-behavior: auto !important; }
        }
    </style>

    @stack('styles')
</head>
<body class="min-h-screen bg-armor font-sans text-ink antialiased">

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

    {{-- Страницы слова/выражения могут выставить window.philContext ДО
         подключения виджета (см. phil-widget.blade.php) --}}
    @stack('phil-context')

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

        // Переключатель светлой/тёмной темы (кнопка в шапке, см. site-header.blade.php)
        document.addEventListener('DOMContentLoaded', function () {
            var themeToggle = document.getElementById('themeToggle');
            if (!themeToggle) return;
            themeToggle.addEventListener('click', function () {
                var isDark = document.documentElement.classList.toggle('dark');
                try { localStorage.setItem('pe-theme', isDark ? 'dark' : 'light'); } catch (e) {}
            });
        });
    </script>

    @stack('scripts')
</body>
</html>
