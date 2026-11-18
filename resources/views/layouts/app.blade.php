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

    {{-- Фавикон — тот же значок, что и в шапке (public/images/logo-icon.svg,
         x-logo). SVG — основной вариант (векторный, чёткий на любом
         разрешении), PNG/ICO ниже — запасной вариант для браузеров без
         поддержки SVG-фавиконок. Все перерендерены из одного SVG. --}}
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">

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

    {{-- Шрифты: Unbounded для заголовков (font-display), Manrope для текста —
         у Manrope есть кириллица (проверено: диапазон U+0400-045F в исходном
         @font-face), поэтому русский и таджикский текст не проваливается на
         системный шрифт. --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Unbounded:wght@500;600;700;800&family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    {{--
        Tailwind собирается локально (npm run build) по tailwind.config.js —
        там же палитра, шкала скруглений и тени. Раньше здесь висел Play CDN,
        который компилировал стили в браузере ученика на каждой загрузке и
        делал сайт зависимым от стороннего домена.

        Тема «Philip Elite»: тёмно-синий с золотом, антиква в заголовках,
        переключаемая тёмная тема через класс .dark. Цвета Tailwind ссылаются
        на CSS-переменные ниже, поэтому обе темы работают без правки разметки.

        Ссылка на собранный CSS стоит ДО <style> ниже: инлайновые правила
        должны перекрывать утилиты — так же, как это было с CDN.
    --}}
    @vite(['resources/css/app.css'])
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script src="{{ asset('assets/js/pronounce.js') }}"></script>
    <script src="{{ asset('assets/js/flashcard-deck.js') }}"></script>

    <style>
        /* ---------- Цветовые токены: светлая тема (по умолчанию) ----------
           Новая палитра (заменяет тёмно-синий+золото на тёмно-синий+голубой,
           без тёплых акцентов — задача была явно убрать золото, а не
           подмешать его к новому синему). --pe-navy/--pe-navy2/--pe-sky и
           --pe-gold сделаны неизменными между темами (как было раньше у
           navy/navy2) — --pe-gold теперь равен --pe-sky: роль «яркого
           акцента» осталась, сам акцент — больше не золотой. --pe-skylight/
           --pe-sun — соседние по тону остановки из голубого градиента шкалы
           CEFR на главной, а не рандомные цвета, чтобы вся палитра читалась
           как одна согласованная лестница синего. */
        :root {
            --pe-ink: #142033;
            --pe-armor: #F5F8FC;
            --pe-armor2: #FFFFFF;
            --pe-surface2: #EDF2F9;
            --pe-line: #E1E8F1;
            --pe-brand: #1E3A8A;
            --pe-sky: #2FA8E0;
            --pe-skylight: #8FC2EF;
            --pe-sun: #5FA6E6;
            --pe-navy: #0B1F3A;
            --pe-navy2: #152A63;
            --pe-gold: #2FA8E0;

            /* Каналы R G B без функции-обёртки — только для tailwind.config.js
               (colors.*: 'rgb(var(--pe-X-rgb) / <alpha-value>)'), чтобы
               bg-ink/50 и подобные классы с дробной прозрачностью реально
               попадали в собранный CSS: Tailwind не умеет накладывать
               альфа-канал на var(), которая уже сама по себе целый цвет
               (#hex) — только на «сырые» каналы через rgb(... / N). Сами
               --pe-X (сверху) не трогаем и не убираем — от них напрямую
               зависят дашборд (--accent + color-mix()), phil-widget,
               phil-mascot и градиенты на нескольких страницах; дублируем
               значения, а не заменяем. */
            --pe-ink-rgb: 20 32 51;
            --pe-armor-rgb: 245 248 252;
            --pe-armor2-rgb: 255 255 255;
            --pe-surface2-rgb: 237 242 249;
            --pe-line-rgb: 225 232 241;
            --pe-brand-rgb: 30 58 138;
            --pe-sky-rgb: 47 168 224;
            --pe-skylight-rgb: 143 194 239;
            --pe-sun-rgb: 95 166 230;
            --pe-navy-rgb: 11 31 58;
            --pe-navy2-rgb: 21 42 99;
            --pe-gold-rgb: 47 168 224;

            /* Маскот — красная панда (components/mascot.blade.php), заменил
               прежнюю сову. Цвета бренда, а не UI-токены — одинаковые в
               обеих темах намеренно (как --pe-navy/--pe-gold выше): значок
               одного и того же зверя не должен перекрашиваться вместе с
               интерфейсом вокруг него. */
            --pe-mascot-red: #E0662B;
            --pe-mascot-red-deep: #D9622C;
            --pe-mascot-cream: #FFFFFF;
            --pe-mascot-dark: #3A2820;
        }

        /* ---------- Тёмная тема: включается классом .dark на <html> ---------- */
        html.dark {
            --pe-ink: #E7EEF7;
            --pe-armor: #0B1F3A;
            --pe-armor2: #152A63;
            --pe-surface2: #1B3A66;
            --pe-line: #2A4570;
            --pe-brand: #5FA6E6;
            --pe-sky: #2FA8E0;
            --pe-skylight: #BEDBF7;
            --pe-sun: #8FC2EF;
            --pe-navy: #0B1F3A;
            --pe-navy2: #152A63;

            --pe-ink-rgb: 231 238 247;
            --pe-armor-rgb: 11 31 58;
            --pe-armor2-rgb: 21 42 99;
            --pe-surface2-rgb: 27 58 102;
            --pe-line-rgb: 42 69 112;
            --pe-brand-rgb: 95 166 230;
            --pe-sky-rgb: 47 168 224;
            --pe-skylight-rgb: 190 219 247;
            --pe-sun-rgb: 143 194 239;
            --pe-navy-rgb: 11 31 58;
            --pe-navy2-rgb: 21 42 99;
            --pe-gold: #2FA8E0;
            --pe-gold-rgb: 47 168 224;
        }

        /* Плавное появление элементов при скролле (см. IntersectionObserver внизу файла).
           Скрывающее opacity:0 включено ТОЛЬКО классом .js-reveal, который
           навешивает сам скрипт реализации — если JS не выполнился (ошибка,
           заблокирован, не успел загрузиться), класса не будет и контент
           останется видимым по умолчанию, а не спрячется навсегда. */
        .js-reveal [data-reveal] {
            opacity: 0;
            transform: translateY(24px);
            transition: opacity .5s ease-out, transform .5s ease-out;
        }
        [data-reveal].is-visible {
            opacity: 1;
            transform: translateY(0);
        }

        /* Прогресс-бар, который заполняется при попадании в зону видимости
           (см. IntersectionObserver внизу файла) — стартует с 0% ширины. */
        [data-fill-width] {
            width: 0%;
            transition: width 1s ease-out;
        }

        /* Подъём карточки + смена тени при наведении — заметный, но не «прыжок» */
        .card-lift {
            transition: transform .35s ease, box-shadow .35s ease, border-color .35s ease;
        }
        .card-lift:hover {
            transform: translateY(-6px);
            box-shadow: 0 20px 45px -18px rgba(26, 26, 46, .3);
        }

        body { transition: background-color .3s ease, color .3s ease; }

        /* Уважаем предпочтение отключить анимации */
        @media (prefers-reduced-motion: reduce) {
            [data-reveal] { opacity: 1 !important; transform: none !important; transition: none !important; }
            [data-fill-width] { transition: none !important; }
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

    {{-- Общий чат гостям недоступен вообще (маршруты chat.* — за auth-
         мидлварью), поэтому и панель для них не рендерим: иначе вкладка
         была бы видна, а бутстрап-запрос при открытии просто ловил бы
         редирект на логин. --}}
    @auth
        <x-chat-drawer />
        <script src="{{ asset('assets/js/chat.js') }}"></script>
    @endauth

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

            // Класс, включающий opacity:0 в CSS, навешиваем только теперь,
            // когда мы уже точно знаем, что дальше сможем всё раскрыть —
            // это и есть фолбэк на случай падения скрипта (см. .js-reveal
            // в <style> выше): без этой строки ничего не прячется вообще.
            document.documentElement.classList.add('js-reveal');

            // То, что уже видно в первом экране, раскрываем сразу по
            // таймеру-каскаду, а не ждём IntersectionObserver — на
            // страницах с несколькими ОЧЕНЬ высокими блоками (например,
            // /exercises: все упражнения уровня в одной карточке на тысячи
            // пикселей) видимая при загрузке часть карточки может не
            // набрать порог threshold: 0.12 от её полной высоты, и колбэк
            // не срабатывает до скролла — баг был именно в этом, а не в
            // самом IntersectionObserver. Ниже сгиба поведение прежнее.
            var viewportHeight = window.innerHeight || document.documentElement.clientHeight;
            var immediate = [];
            var deferred = [];
            targets.forEach(function (el) {
                var rect = el.getBoundingClientRect();
                if (rect.top < viewportHeight && rect.bottom > 0) {
                    immediate.push(el);
                } else {
                    deferred.push(el);
                }
            });

            immediate.forEach(function (el, i) {
                setTimeout(function () { el.classList.add('is-visible'); }, i * 60);
            });

            if (deferred.length) {
                var observer = new IntersectionObserver(function (entries) {
                    entries.forEach(function (entry) {
                        if (entry.isIntersecting) {
                            entry.target.classList.add('is-visible');
                            observer.unobserve(entry.target);
                        }
                    });
                }, { threshold: 0.12, rootMargin: '0px 0px -40px 0px' });

                deferred.forEach(function (el) { observer.observe(el); });
            }

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

            // Прогресс-бары, заполняющиеся при попадании в область видимости:
            // <div data-fill-width="72"></div> — ширина в процентах (0-100),
            // сама полоска красится/оформляется классами на месте использования.
            var fillBars = document.querySelectorAll('[data-fill-width]');
            if (fillBars.length) {
                if (prefersReduced || !('IntersectionObserver' in window)) {
                    fillBars.forEach(function (el) { el.style.width = (el.dataset.fillWidth || 0) + '%'; });
                } else {
                    var fillObserver = new IntersectionObserver(function (entries) {
                        entries.forEach(function (entry) {
                            if (!entry.isIntersecting) return;
                            fillObserver.unobserve(entry.target);
                            var el = entry.target;
                            requestAnimationFrame(function () { el.style.width = (el.dataset.fillWidth || 0) + '%'; });
                        });
                    }, { threshold: 0.4 });
                    fillBars.forEach(function (el) { fillObserver.observe(el); });
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
