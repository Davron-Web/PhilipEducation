{{-- Гостевая главная страница. Использует общий layout (layouts.app) —
     шапку, подвал и виджет Phil со всего сайта — чтобы дизайн был
     полностью единым, а не отдельной «одноразовой» страницей. --}}
@extends('layouts.app')

@section('title', 'Philip Education')
@section('page_title', 'Учите английский с удовольствием')
@section('meta_description', 'Philip Education — платформа для изучения английского языка: уроки, грамматика, словарь, выражения, упражнения, тесты, книги и AI-ассистент Phil.')

@push('styles')
    <style>
        .flip-card { perspective: 1200px; }
        .flip-card-inner {
            position: relative;
            transform-style: preserve-3d;
            transition: transform .55s cubic-bezier(.22,1,.36,1);
        }
        .flip-card.is-flipped .flip-card-inner { transform: rotateY(180deg); }
        .flip-face { backface-visibility: hidden; -webkit-backface-visibility: hidden; }
        .flip-back { position: absolute; inset: 0; transform: rotateY(180deg); }

        @keyframes home-float-a { 0%,100% { transform: translate(0,0) rotate(-6deg); } 50% { transform: translate(6px,-14px) rotate(-3deg); } }
        @keyframes home-float-b { 0%,100% { transform: translate(0,0) rotate(4deg); } 50% { transform: translate(-8px,10px) rotate(7deg); } }
        @keyframes home-float-c { 0%,100% { transform: translate(0,0) rotate(-2deg); } 50% { transform: translate(5px,12px) rotate(-5deg); } }
        .home-float-a { animation: home-float-a 7s ease-in-out infinite; }
        .home-float-b { animation: home-float-b 8.5s ease-in-out infinite; }
        .home-float-c { animation: home-float-c 6.5s ease-in-out infinite; }
        @media (prefers-reduced-motion: reduce) { .home-float-a, .home-float-b, .home-float-c { animation: none; } }

        .hero-mesh {
            background:
                radial-gradient(60% 60% at 18% 18%, rgb(79 70 229 / .14), transparent 60%),
                radial-gradient(50% 50% at 85% 12%, rgb(6 182 212 / .12), transparent 60%),
                radial-gradient(45% 45% at 78% 82%, rgb(249 115 22 / .10), transparent 60%);
        }
        html.dark .hero-mesh {
            background:
                radial-gradient(60% 60% at 18% 18%, rgb(99 102 241 / .22), transparent 60%),
                radial-gradient(50% 50% at 85% 12%, rgb(34 211 238 / .16), transparent 60%),
                radial-gradient(45% 45% at 78% 82%, rgb(251 146 60 / .14), transparent 60%);
        }
    </style>
@endpush

@section('content')

    {{-- ===================== HERO ===================== --}}
    <section class="hero-mesh relative overflow-hidden">
        <div class="mx-auto grid max-w-7xl items-center gap-12 px-4 py-16 sm:px-6 lg:grid-cols-2 lg:px-8 lg:py-24">
            <div data-reveal>
                <span class="inline-flex items-center gap-2 rounded-full border border-line bg-armor2 px-3.5 py-1.5 text-[12.5px] font-bold text-brand shadow-soft">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M11 2 3 14h7l-1 8 9-13h-7l1-7Z" /></svg>
                    AI-ассистент Phil уже внутри
                </span>

                <h1 class="mt-5 font-display text-[40px] font-extrabold leading-[1.08] tracking-tight text-ink sm:text-[52px] lg:text-[58px]">
                    Английский, который<br>
                    <span class="bg-gradient-to-r from-brand to-sky bg-clip-text text-transparent">вдохновляет</span> говорить
                </h1>

                <p class="mt-5 max-w-xl text-[17px] leading-relaxed text-ink/60 sm:text-[18px]">
                    Уроки, грамматика, живой словарь, упражнения и тесты — в одном месте, с честным прогрессом и AI-помощником, который объясняет так, что запоминается.
                </p>

                <div class="mt-8 flex flex-wrap items-center gap-3.5">
                    <a href="{{ route('register') }}" class="group inline-flex items-center gap-2 rounded-full bg-gradient-to-r from-brand to-sky px-6 py-3.5 text-[15px] font-bold text-white shadow-lg shadow-brand/25 transition hover:-translate-y-0.5 hover:shadow-xl hover:shadow-brand/30">
                        Начать учиться
                        <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round" class="transition group-hover:translate-x-0.5"><path d="M5 12h14M13 5l7 7-7 7" /></svg>
                    </a>
                    <a href="#lessons" class="inline-flex items-center gap-2 rounded-full border-2 border-line bg-armor2 px-6 py-3.5 text-[15px] font-bold text-ink transition hover:border-brand/40 hover:bg-surface2">
                        Посмотреть уровни
                    </a>
                </div>

                <div class="mt-10 flex flex-wrap items-center gap-x-8 gap-y-3 border-t border-line pt-6 text-[13.5px] text-ink/60">
                    <div><span class="font-mono text-[19px] font-bold text-ink" data-counter data-target="{{ $totalUsers }}">0</span> учеников</div>
                    <div><span class="font-mono text-[19px] font-bold text-ink" data-counter data-target="{{ $totalWords }}">0</span> слов в словаре</div>
                    <div><span class="font-mono text-[19px] font-bold text-ink" data-counter data-target="{{ $totalLessons }}">0</span> уроков</div>
                </div>
            </div>

            {{-- Абстрактная композиция: градиентные пятна + плавающие карточки-слова --}}
            <div class="relative hidden h-[440px] lg:block" data-reveal>
                <div class="absolute left-8 top-4 h-56 w-56 rounded-full bg-gradient-to-br from-brand/25 to-sky/15 blur-2xl"></div>
                <div class="absolute bottom-0 right-4 h-64 w-64 rounded-full bg-gradient-to-br from-sun/20 to-brand/10 blur-2xl"></div>

                <div class="home-float-a absolute left-[16%] top-[10%] w-56 rounded-2xl border border-line bg-armor2 p-5 shadow-2xl shadow-brand/10">
                    <p class="text-[11px] font-bold uppercase tracking-wider text-ink/40">Слово дня</p>
                    <p class="mt-1 font-display text-2xl font-bold text-ink">resilient</p>
                    <p class="font-mono text-[13px] text-sky">/rɪˈzɪliənt/</p>
                    <p class="mt-2 text-[14px] font-semibold text-brand">стойкий, жизнестойкий</p>
                </div>

                <div class="home-float-b absolute right-[8%] top-[28%] rounded-2xl border border-line bg-armor2 px-4 py-3 shadow-xl">
                    <p class="font-display text-[15px] font-bold text-ink">serendipity <span class="text-ink/40">— удача</span></p>
                </div>

                <div class="home-float-c absolute bottom-[10%] left-[26%] rounded-2xl border border-line bg-armor2 px-4 py-3 shadow-xl">
                    <p class="font-display text-[15px] font-bold text-ink">eloquent <span class="text-ink/40">— красноречивый</span></p>
                </div>

                <div class="home-float-b absolute right-[14%] bottom-[6%] grid h-16 w-16 place-items-center rounded-2xl bg-gradient-to-br from-sun to-brand text-white shadow-xl">
                    <span class="font-display text-lg font-extrabold">B1</span>
                </div>
            </div>
        </div>
    </section>

    {{-- ===================== РАЗДЕЛЫ ПЛАТФОРМЫ ===================== --}}
    @php
        $sections = [
            ['label' => 'Уроки', 'desc' => 'Структурированные уроки от A1 до C1: видео, тексты и практика в каждой теме.', 'route' => 'lessons.index', 'color' => 'brand', 'icon' => '<path d="M22 10 12 5 2 10l10 5 10-5Z" /><path d="M6 12v5c0 1.5 3 3 6 3s6-1.5 6-3v-5" />'],
            ['label' => 'Грамматика', 'desc' => 'Разбор всех тем — от Present Simple до сложных придаточных предложений.', 'route' => 'grammartopics.index', 'color' => 'sky', 'icon' => '<path d="M12 20h9" /><path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5Z" />'],
            ['label' => 'Словарь', 'desc' => 'Более 3000 слов с транскрипцией, переводом и озвучкой произношения.', 'route' => 'words.index', 'color' => 'sun', 'icon' => '<path d="m5 8 6 6" /><path d="m4 14 6-6 2-3" /><path d="M2 5h12" /><path d="m22 22-5-10-5 10" /><path d="M14 18h6" />'],
            ['label' => 'Выражения', 'desc' => 'Идиомы, фразовые глаголы, пословицы и коллокации — живой английский язык.', 'route' => 'expressions.index', 'color' => 'brand', 'icon' => '<path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z" />'],
            ['label' => 'Упражнения', 'desc' => 'Заполнение пропусков, сопоставление и перевод — для закрепления каждой темы.', 'route' => 'exercises.index', 'color' => 'sky', 'icon' => '<path d="M14.4 14.4 9.6 9.6" /><path d="M18.657 21.485a2 2 0 1 1-2.829-2.828l6.364-6.364a2 2 0 1 1 2.829 2.829z" /><path d="M6.404 12.768a2 2 0 1 1-2.829-2.829l1.768-1.768a2 2 0 1 1-2.828-2.828l1.768-1.768a2 2 0 1 1 2.828 2.828l-1.768 1.768a2 2 0 1 1 2.829 2.829z" />'],
            ['label' => 'Тесты', 'desc' => 'Проверь себя после каждого урока — мгновенный результат и разбор ошибок.', 'route' => 'tests.index', 'color' => 'sun', 'icon' => '<path d="m3 17 2 2 4-4" /><path d="m3 7 2 2 4-4" /><path d="M13 6h8" /><path d="M13 12h8" /><path d="M13 18h8" />'],
            ['label' => 'IELTS', 'desc' => 'Reading, Listening, Writing и Speaking — с AI-проверкой эссе и заданиями по формату экзамена.', 'route' => 'ielts.index', 'color' => 'brand', 'icon' => '<path d="M12 20h9" /><path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5Z" />'],
            ['label' => 'Книги', 'desc' => 'Адаптированная английская литература, подобранная по уровню владения языком.', 'route' => 'books.index', 'color' => 'sky', 'icon' => '<path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20" /><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2Z" />'],
            ['label' => 'Достижения', 'desc' => 'Собирай награды, следи за streak и прогрессом — учёба, в которую хочется возвращаться.', 'route' => 'achievements.index', 'color' => 'sun', 'icon' => '<circle cx="12" cy="8" r="6" /><path d="M15.477 12.89 17 22l-5-3-5 3 1.523-9.11" />'],
        ];
    @endphp

    <section id="sections" class="border-t border-line bg-surface2/50 py-20 sm:py-28">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="mx-auto max-w-2xl text-center" data-reveal>
                <h2 class="font-display text-[32px] font-extrabold tracking-tight text-ink sm:text-[40px]">Всё для учёбы — в одном месте</h2>
                <p class="mt-3 text-[16px] text-ink/60">От первого урока до свободного владения языком: каждый раздел закрывает свою часть пути.</p>
            </div>

            <div class="mt-14 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($sections as $s)
                    <a href="{{ route($s['route']) }}" data-reveal class="card-lift group rounded-3xl border border-line bg-armor2 p-7">
                        <span class="grid h-12 w-12 place-items-center rounded-2xl bg-{{ $s['color'] }}/10 text-{{ $s['color'] }}">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">{!! $s['icon'] !!}</svg>
                        </span>
                        <h3 class="mt-5 font-display text-[19px] font-bold text-ink">{{ $s['label'] }}</h3>
                        <p class="mt-2 text-[14.5px] leading-relaxed text-ink/60">{{ $s['desc'] }}</p>
                        <span class="mt-4 inline-flex items-center gap-1 text-[13.5px] font-bold text-{{ $s['color'] }}">
                            Перейти <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" class="transition group-hover:translate-x-1"><path d="M5 12h14M13 5l7 7-7 7" /></svg>
                        </span>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ===================== УРОВНИ (реальные данные) ===================== --}}
    <section id="lessons" class="py-20 sm:py-28">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="mx-auto max-w-2xl text-center" data-reveal>
                <h2 class="font-display text-[32px] font-extrabold tracking-tight text-ink sm:text-[40px]">Выбери свой уровень</h2>
                <p class="mt-3 text-[16px] text-ink/60">От первого «Hello» до свободной академической речи — шесть уровней по шкале CEFR.</p>
            </div>

            <div class="mt-14 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($levels as $level)
                    <div data-reveal class="card-lift relative overflow-hidden rounded-3xl border p-6 {{ $level['code'] === 'B1' ? 'border-brand/40 bg-gradient-to-br from-brand/5 to-sky/5' : 'border-line bg-armor2' }}">
                        @if ($level['code'] === 'B1')
                            <span class="absolute right-5 top-5 rounded-full bg-brand px-2.5 py-1 text-[10.5px] font-extrabold text-white">Популярно</span>
                        @endif
                        <span class="grid h-11 w-11 place-items-center rounded-xl bg-gradient-to-br from-brand to-sky font-display text-sm font-extrabold text-white">{{ $level['code'] }}</span>
                        <h3 class="mt-4 font-display text-[18px] font-bold text-ink">{{ $level['name'] }}</h3>
                        <p class="mt-1.5 text-[13.5px] leading-relaxed text-ink/60">{{ \Illuminate\Support\Str::limit($level['description'], 90) }}</p>
                        <p class="mt-3 text-[12.5px] font-bold text-ink/40">{{ $level['lessons_count'] }} {{ $level['lessons_count'] === 1 ? 'урок' : 'уроков' }}</p>
                        <a href="{{ route('register') }}" class="mt-4 block rounded-xl {{ $level['code'] === 'B1' ? 'bg-gradient-to-r from-brand to-sky text-white' : 'border-2 border-line text-ink hover:border-brand/40' }} py-2.5 text-center text-[13.5px] font-bold transition">Начать с {{ $level['code'] }}</a>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ===================== СЛОВАРЬ + ФЛЕШ-КАРТОЧКА ===================== --}}
    <section id="vocabulary" class="border-t border-line bg-surface2/50 py-20 sm:py-28">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex flex-wrap items-end justify-between gap-4" data-reveal>
                <div>
                    <h2 class="font-display text-[32px] font-extrabold tracking-tight text-ink sm:text-[40px]">Учи слова с контекстом</h2>
                    <p class="mt-2 text-[16px] text-ink/60">Каждое слово — с транскрипцией, переводом, произношением и живым примером.</p>
                </div>
                <a href="{{ route('register') }}" class="hidden items-center gap-1.5 text-[14px] font-bold text-brand sm:inline-flex">Весь словарь <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6"><path d="M5 12h14M13 5l7 7-7 7" /></svg></a>
            </div>

            <div class="mt-10 grid items-center gap-10 lg:grid-cols-[1fr_auto]">
                <div class="grid gap-4 sm:grid-cols-2">
                    <div data-reveal class="card-lift rounded-2xl border border-line bg-armor2 p-5">
                        <p class="font-display text-[19px] font-bold text-ink">ubiquitous</p>
                        <p class="font-mono text-[13px] text-sky">/juːˈbɪkwɪtəs/</p>
                        <p class="mt-2 text-[14.5px] font-semibold text-brand">вездесущий</p>
                        <p class="mt-2 text-[13.5px] italic leading-relaxed text-ink/50">«Smartphones have become ubiquitous.»</p>
                    </div>
                    <div data-reveal class="card-lift rounded-2xl border border-line bg-armor2 p-5">
                        <p class="font-display text-[19px] font-bold text-ink">meticulous</p>
                        <p class="font-mono text-[13px] text-sky">/məˈtɪkjələs/</p>
                        <p class="mt-2 text-[14.5px] font-semibold text-brand">дотошный</p>
                        <p class="mt-2 text-[13.5px] italic leading-relaxed text-ink/50">«She is meticulous about every detail.»</p>
                    </div>
                    <div data-reveal class="card-lift rounded-2xl border border-line bg-armor2 p-5">
                        <p class="font-display text-[19px] font-bold text-ink">eloquent</p>
                        <p class="font-mono text-[13px] text-sky">/ˈeləkwənt/</p>
                        <p class="mt-2 text-[14.5px] font-semibold text-brand">красноречивый</p>
                        <p class="mt-2 text-[13.5px] italic leading-relaxed text-ink/50">«She gave an eloquent speech.»</p>
                    </div>
                    <div data-reveal class="card-lift rounded-2xl border border-line bg-armor2 p-5">
                        <p class="font-display text-[19px] font-bold text-ink">perseverance</p>
                        <p class="font-mono text-[13px] text-sky">/ˌpɜːsəˈvɪərəns/</p>
                        <p class="mt-2 text-[14.5px] font-semibold text-brand">упорство</p>
                        <p class="mt-2 text-[13.5px] italic leading-relaxed text-ink/50">«Success requires perseverance.»</p>
                    </div>
                </div>

                <div class="flip-card mx-auto h-56 w-72 cursor-pointer" id="homeFlashcard" data-reveal>
                    <div class="flip-card-inner h-full w-full">
                        <div class="flip-face flex h-full flex-col items-center justify-center rounded-3xl border border-line bg-gradient-to-br from-armor2 to-surface2 p-6 text-center shadow-xl">
                            <p class="text-[11px] font-bold uppercase tracking-widest text-ink/40">Слово</p>
                            <p class="mt-3 font-display text-[26px] font-extrabold text-ink">wanderlust</p>
                            <p class="mt-1 font-mono text-[13px] text-sky">/ˈwɒndəlʌst/</p>
                            <p class="mt-5 text-[12px] text-ink/40">Нажми, чтобы перевернуть</p>
                        </div>
                        <div class="flip-face flip-back flex h-full flex-col items-center justify-center rounded-3xl bg-gradient-to-br from-brand via-brand to-sky p-6 text-center text-white shadow-xl">
                            <p class="text-[11px] font-bold uppercase tracking-widest text-white/70">Перевод</p>
                            <p class="mt-3 font-display text-[24px] font-extrabold">страсть к путешествиям</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ===================== PHIL ===================== --}}
    <section class="py-20 sm:py-24">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div data-reveal class="grid items-center gap-10 rounded-[32px] border border-line bg-armor2 p-8 shadow-soft sm:p-12 lg:grid-cols-2">
                <div>
                    <span class="inline-flex items-center gap-2 rounded-full bg-brand/10 px-3.5 py-1.5 text-[12.5px] font-bold text-brand">AI-помощник</span>
                    <h2 class="mt-4 font-display text-[28px] font-extrabold tracking-tight text-ink sm:text-[34px]">Знакомься, это <span class="bg-gradient-to-r from-brand to-sky bg-clip-text text-transparent">Phil</span></h2>
                    <p class="mt-3 max-w-md text-[15.5px] leading-relaxed text-ink/60">Задай вопрос про грамматику, слово или произношение прямо на сайте — Phil ответит с примерами и объяснит так, чтобы точно запомнилось. Открыть его можно в любой момент — кнопка внизу справа.</p>
                </div>
                <div class="space-y-3 rounded-3xl bg-surface2 p-5">
                    <div class="flex items-end gap-2">
                        <span class="grid h-7 w-7 shrink-0 place-items-center rounded-full bg-gradient-to-br from-brand to-sky text-white">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10" /><path d="M8 14s1.5 2 4 2 4-2 4-2" /></svg>
                        </span>
                        <div class="max-w-[80%] rounded-2xl rounded-bl-sm bg-armor2 px-3.5 py-2.5 text-[13.5px] leading-relaxed text-ink shadow-sm">В чём разница между «a few» и «few»?</div>
                    </div>
                    <div class="flex justify-end">
                        <div class="max-w-[80%] rounded-2xl rounded-br-sm bg-gradient-to-br from-brand to-sky px-3.5 py-2.5 text-[13.5px] leading-relaxed text-white shadow-sm">«A few» значит «несколько» — I have a few friends. А «few» значит «почти нет» 😅</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ===================== ДОСТИЖЕНИЯ (иллюстративно) ===================== --}}
    <section id="achievements" class="border-t border-line bg-surface2/50 py-20 sm:py-28">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="mx-auto max-w-2xl text-center" data-reveal>
                <h2 class="font-display text-[32px] font-extrabold tracking-tight text-ink sm:text-[40px]">Учёба, в которую хочется возвращаться</h2>
                <p class="mt-3 text-[16px] text-ink/60">Собирай награды за streak, пройденные темы и новые слова.</p>
            </div>

            <div class="mt-14 grid grid-cols-2 gap-5 sm:grid-cols-3 lg:grid-cols-5">
                @foreach ([
                    ['label' => 'Первый урок', 'icon' => '<path d="M22 10 12 5 2 10l10 5 10-5Z" /><path d="M6 12v5c0 1.5 3 3 6 3s6-1.5 6-3v-5" />'],
                    ['label' => 'Неделя подряд', 'icon' => '<path d="M12 2c1 3-2 4-2 7a4 4 0 0 0 8 0c0-1-.4-2-1-3 2 1 3 3.5 3 6a7 7 0 1 1-14 0c0-4 2-6 3-7 1-1 2-2 3-3Z" fill="currentColor" stroke="none" />'],
                    ['label' => '100 слов', 'icon' => '<path d="m5 8 6 6" /><path d="m4 14 6-6 2-3" /><path d="M2 5h12" /><path d="m22 22-5-10-5 10" /><path d="M14 18h6" />'],
                    ['label' => 'Тест на 100%', 'icon' => '<path d="m3 17 2 2 4-4" /><path d="M13 6h8M13 12h8M13 18h8" />'],
                    ['label' => 'Уровень A2', 'icon' => '<circle cx="12" cy="8" r="6" /><path d="M15.477 12.89 17 22l-5-3-5 3 1.523-9.11" />'],
                ] as $badge)
                    <div data-reveal class="flex flex-col items-center rounded-3xl border border-line bg-armor2 p-6 text-center">
                        <span class="grid h-16 w-16 place-items-center rounded-full text-white shadow-lg" style="background: conic-gradient(from 220deg, var(--pe-sun), var(--pe-brand) 45%, var(--pe-sky) 100%);">
                            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">{!! $badge['icon'] !!}</svg>
                        </span>
                        <p class="mt-3 text-[13px] font-bold leading-tight text-ink">{{ $badge['label'] }}</p>
                    </div>
                @endforeach

                <div data-reveal class="flex flex-col items-center rounded-3xl border border-line bg-armor2 p-6 text-center opacity-60 grayscale">
                    <span class="grid h-16 w-16 place-items-center rounded-full bg-surface2 text-ink/40">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="11" width="16" height="10" rx="2" /><path d="M8 11V7a4 4 0 0 1 8 0v4" /></svg>
                    </span>
                    <p class="mt-3 text-[13px] font-bold leading-tight text-ink/50">Мастер грамматики</p>
                </div>
            </div>
        </div>
    </section>

    {{-- ===================== CTA ===================== --}}
    <section class="py-20 sm:py-24">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div data-reveal class="relative overflow-hidden rounded-[32px] bg-gradient-to-br from-brand via-brand to-sky px-8 py-16 text-center sm:px-16">
                <div class="pointer-events-none absolute -left-10 -top-10 h-56 w-56 rounded-full bg-white/10 blur-3xl"></div>
                <div class="pointer-events-none absolute -bottom-16 -right-10 h-64 w-64 rounded-full bg-sun/20 blur-3xl"></div>
                <h2 class="relative font-display text-[30px] font-extrabold text-white sm:text-[38px]">Готов заговорить свободно?</h2>
                <p class="relative mx-auto mt-3 max-w-md text-[15.5px] text-white/85">Присоединяйся бесплатно — первые уроки открыты для всех.</p>
                <a href="{{ route('register') }}" class="relative mt-7 inline-flex items-center gap-2 rounded-full bg-white px-7 py-3.5 text-[15px] font-bold text-brand shadow-xl transition hover:-translate-y-0.5">
                    Создать аккаунт бесплатно
                    <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 5l7 7-7 7" /></svg>
                </a>
            </div>
        </div>
    </section>

@endsection

@push('scripts')
    <script>
        var homeFlashcard = document.getElementById('homeFlashcard');
        if (homeFlashcard) {
            homeFlashcard.addEventListener('click', function () {
                this.classList.toggle('is-flipped');
            });
        }
    </script>
@endpush
