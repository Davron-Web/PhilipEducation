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
            transition: transform .5s cubic-bezier(.22,1,.36,1);
        }
        .flip-card.is-flipped .flip-card-inner { transform: rotateY(180deg); }
        .flip-face { backface-visibility: hidden; -webkit-backface-visibility: hidden; }
        .flip-back { position: absolute; inset: 0; transform: rotateY(180deg); }

        .hero-mesh {
            background: radial-gradient(80% 60% at 80% 0%, rgb(67 56 202 / .07), transparent 60%);
        }
        html.dark .hero-mesh {
            background: radial-gradient(80% 60% at 80% 0%, rgb(99 102 241 / .14), transparent 60%);
        }
    </style>
@endpush

@section('content')

    {{-- ===================== HERO ===================== --}}
    <section class="hero-mesh relative overflow-hidden">
        <div class="mx-auto grid max-w-7xl items-center gap-12 px-4 py-16 sm:px-6 lg:grid-cols-2 lg:px-8 lg:py-24">
            <div data-reveal>
                <span class="inline-flex items-center gap-2 rounded-full border border-line bg-armor2 px-3.5 py-1.5 text-[12.5px] font-semibold text-brand shadow-soft">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M11 2 3 14h7l-1 8 9-13h-7l1-7Z" /></svg>
                    AI-ассистент Phil уже внутри
                </span>

                <h1 class="mt-5 font-display text-[38px] font-extrabold leading-[1.12] tracking-tight text-ink sm:text-[48px] lg:text-[54px]">
                    Английский, который<br>
                    <span class="text-brand">вдохновляет</span> говорить
                </h1>

                <p class="mt-5 max-w-xl text-[17px] leading-relaxed text-ink/60">
                    Уроки, грамматика, живой словарь, упражнения и тесты — в одном месте, с честным прогрессом и AI-помощником, который объясняет так, что запоминается.
                </p>

                <div class="mt-8 flex flex-wrap items-center gap-3">
                    <a href="{{ route('register') }}" class="group inline-flex items-center gap-2 rounded-lg bg-brand px-6 py-3 text-[15px] font-semibold text-white shadow-soft transition hover:bg-brand/90">
                        Начать учиться
                        <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round" class="transition group-hover:translate-x-0.5"><path d="M5 12h14M13 5l7 7-7 7" /></svg>
                    </a>
                    <a href="#lessons" class="inline-flex items-center gap-2 rounded-lg border border-line bg-armor2 px-6 py-3 text-[15px] font-semibold text-ink transition hover:border-brand/40 hover:bg-surface2">
                        Посмотреть уровни
                    </a>
                </div>

                <div class="mt-10 flex flex-wrap items-center gap-x-8 gap-y-3 border-t border-line pt-6 text-[13.5px] text-ink/60">
                    <div><span class="font-mono text-[19px] font-bold text-ink" data-counter data-target="{{ $totalUsers }}">0</span> учеников</div>
                    <div><span class="font-mono text-[19px] font-bold text-ink" data-counter data-target="{{ $totalWords }}">0</span> слов в словаре</div>
                    <div><span class="font-mono text-[19px] font-bold text-ink" data-counter data-target="{{ $totalLessons }}">0</span> уроков</div>
                </div>
            </div>

            {{-- Статичная витрина «слово дня» — карточка урока, без анимации и «стикеров» --}}
            <div class="relative hidden lg:block" data-reveal>
                <div class="overflow-hidden rounded-2xl border border-line bg-armor2 shadow-softLg">
                    <div class="flex items-center justify-between border-b border-line px-6 py-4">
                        <p class="text-[11px] font-bold uppercase tracking-wider text-ink/40">Слово дня</p>
                        <span class="rounded-md bg-brand/10 px-2 py-0.5 text-[11px] font-bold text-brand">B1</span>
                    </div>
                    <div class="px-6 py-6">
                        <p class="font-display text-3xl font-bold text-ink">resilient</p>
                        <p class="mt-1 font-mono text-sm text-sky">/rɪˈzɪliənt/</p>
                        <p class="mt-3 text-[15px] font-semibold text-brand">стойкий, жизнестойкий</p>
                        <p class="mt-3 text-sm italic leading-relaxed text-ink/50">«Despite the setbacks, she remained resilient.»</p>
                    </div>
                    <div class="space-y-0 border-t border-line">
                        <div class="flex items-center justify-between px-6 py-3 text-sm">
                            <span class="font-semibold text-ink">serendipity</span>
                            <span class="text-ink/40">удача, счастливая случайность</span>
                        </div>
                        <div class="flex items-center justify-between border-t border-line px-6 py-3 text-sm">
                            <span class="font-semibold text-ink">eloquent</span>
                            <span class="text-ink/40">красноречивый</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ===================== РАЗДЕЛЫ ПЛАТФОРМЫ ===================== --}}
    @php
        $sections = [
            ['label' => 'Уроки', 'desc' => 'Структурированные уроки от A1 до C1: видео, тексты и практика в каждой теме.', 'route' => 'lessons.index', 'icon' => '<path d="M22 10 12 5 2 10l10 5 10-5Z" /><path d="M6 12v5c0 1.5 3 3 6 3s6-1.5 6-3v-5" />'],
            ['label' => 'Грамматика', 'desc' => 'Разбор всех тем — от Present Simple до сложных придаточных предложений.', 'route' => 'grammartopics.index', 'icon' => '<path d="M12 20h9" /><path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5Z" />'],
            ['label' => 'Словарь', 'desc' => 'Более 3000 слов с транскрипцией, переводом и озвучкой произношения.', 'route' => 'words.index', 'icon' => '<path d="m5 8 6 6" /><path d="m4 14 6-6 2-3" /><path d="M2 5h12" /><path d="m22 22-5-10-5 10" /><path d="M14 18h6" />'],
            ['label' => 'Выражения', 'desc' => 'Идиомы, фразовые глаголы, пословицы и коллокации — живой английский язык.', 'route' => 'expressions.index', 'icon' => '<path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z" />'],
            ['label' => 'Упражнения', 'desc' => 'Заполнение пропусков, сопоставление и перевод — для закрепления каждой темы.', 'route' => 'exercises.index', 'icon' => '<path d="M14.4 14.4 9.6 9.6" /><path d="M18.657 21.485a2 2 0 1 1-2.829-2.828l6.364-6.364a2 2 0 1 1 2.829 2.829z" /><path d="M6.404 12.768a2 2 0 1 1-2.829-2.829l1.768-1.768a2 2 0 1 1-2.828-2.828l1.768-1.768a2 2 0 1 1 2.828 2.828l-1.768 1.768a2 2 0 1 1 2.829 2.829z" />'],
            ['label' => 'Тесты', 'desc' => 'Проверь себя после каждого урока — мгновенный результат и разбор ошибок.', 'route' => 'tests.index', 'icon' => '<path d="m3 17 2 2 4-4" /><path d="m3 7 2 2 4-4" /><path d="M13 6h8" /><path d="M13 12h8" /><path d="M13 18h8" />'],
            ['label' => 'IELTS', 'desc' => 'Reading, Listening, Writing и Speaking — с AI-проверкой эссе и заданиями по формату экзамена.', 'route' => 'ielts.index', 'icon' => '<path d="M12 20h9" /><path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5Z" />'],
            ['label' => 'Книги', 'desc' => 'Адаптированная английская литература, подобранная по уровню владения языком.', 'route' => 'books.index', 'icon' => '<path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20" /><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2Z" />'],
            ['label' => 'Достижения', 'desc' => 'Собирай награды, следи за streak и прогрессом — учёба, в которую хочется возвращаться.', 'route' => 'achievements.index', 'icon' => '<circle cx="12" cy="8" r="6" /><path d="M15.477 12.89 17 22l-5-3-5 3 1.523-9.11" />'],
        ];
    @endphp

    <section id="sections" class="border-t border-line bg-surface2/50 py-20 sm:py-28">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="mx-auto max-w-2xl text-center" data-reveal>
                <h2 class="font-display text-[30px] font-extrabold tracking-tight text-ink sm:text-[36px]">Всё для учёбы — в одном месте</h2>
                <p class="mt-3 text-[16px] text-ink/60">От первого урока до свободного владения языком: каждый раздел закрывает свою часть пути.</p>
            </div>

            <div class="mt-14 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($sections as $s)
                    <a href="{{ route($s['route']) }}" data-reveal class="card-lift group rounded-2xl border border-line bg-armor2 p-6">
                        <span class="grid h-11 w-11 place-items-center rounded-lg bg-brand/10 text-brand">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">{!! $s['icon'] !!}</svg>
                        </span>
                        <h3 class="mt-5 font-display text-[18px] font-bold text-ink">{{ $s['label'] }}</h3>
                        <p class="mt-2 text-[14.5px] leading-relaxed text-ink/60">{{ $s['desc'] }}</p>
                        <span class="mt-4 inline-flex items-center gap-1 text-[13.5px] font-semibold text-brand">
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
                <h2 class="font-display text-[30px] font-extrabold tracking-tight text-ink sm:text-[36px]">Выбери свой уровень</h2>
                <p class="mt-3 text-[16px] text-ink/60">От первого «Hello» до свободной академической речи — шесть уровней по шкале CEFR.</p>
            </div>

            <div class="mt-14 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($levels as $level)
                    <div data-reveal class="card-lift relative overflow-hidden rounded-2xl border p-6 {{ $level['code'] === 'B1' ? 'border-brand/40 bg-brand/[.03]' : 'border-line bg-armor2' }}">
                        @if ($level['code'] === 'B1')
                            <span class="absolute right-5 top-5 rounded-md bg-brand px-2 py-1 text-[10.5px] font-bold text-white">Популярно</span>
                        @endif
                        <span class="grid h-11 w-11 place-items-center rounded-lg bg-brand font-display text-sm font-bold text-white">{{ $level['code'] }}</span>
                        <h3 class="mt-4 font-display text-[18px] font-bold text-ink">{{ $level['name'] }}</h3>
                        <p class="mt-1.5 text-[13.5px] leading-relaxed text-ink/60">{{ \Illuminate\Support\Str::limit($level['description'], 90) }}</p>
                        <p class="mt-3 text-[12.5px] font-bold text-ink/40">{{ $level['lessons_count'] }} {{ $level['lessons_count'] === 1 ? 'урок' : 'уроков' }}</p>
                        <a href="{{ route('register') }}" class="mt-4 block rounded-lg {{ $level['code'] === 'B1' ? 'bg-brand text-white hover:bg-brand/90' : 'border border-line text-ink hover:border-brand/40' }} py-2.5 text-center text-[13.5px] font-semibold transition">Начать с {{ $level['code'] }}</a>
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
                    <h2 class="font-display text-[30px] font-extrabold tracking-tight text-ink sm:text-[36px]">Учи слова с контекстом</h2>
                    <p class="mt-2 text-[16px] text-ink/60">Каждое слово — с транскрипцией, переводом, произношением и живым примером.</p>
                </div>
                <a href="{{ route('register') }}" class="hidden items-center gap-1.5 text-[14px] font-semibold text-brand sm:inline-flex">Весь словарь <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6"><path d="M5 12h14M13 5l7 7-7 7" /></svg></a>
            </div>

            <div class="mt-10 grid items-center gap-10 lg:grid-cols-[1fr_auto]">
                <div class="grid gap-4 sm:grid-cols-2">
                    <div data-reveal class="card-lift rounded-xl border border-line bg-armor2 p-5">
                        <p class="font-display text-[18px] font-bold text-ink">ubiquitous</p>
                        <p class="font-mono text-[13px] text-sky">/juːˈbɪkwɪtəs/</p>
                        <p class="mt-2 text-[14px] font-semibold text-brand">вездесущий</p>
                        <p class="mt-2 text-[13.5px] italic leading-relaxed text-ink/50">«Smartphones have become ubiquitous.»</p>
                    </div>
                    <div data-reveal class="card-lift rounded-xl border border-line bg-armor2 p-5">
                        <p class="font-display text-[18px] font-bold text-ink">meticulous</p>
                        <p class="font-mono text-[13px] text-sky">/məˈtɪkjələs/</p>
                        <p class="mt-2 text-[14px] font-semibold text-brand">дотошный</p>
                        <p class="mt-2 text-[13.5px] italic leading-relaxed text-ink/50">«She is meticulous about every detail.»</p>
                    </div>
                    <div data-reveal class="card-lift rounded-xl border border-line bg-armor2 p-5">
                        <p class="font-display text-[18px] font-bold text-ink">eloquent</p>
                        <p class="font-mono text-[13px] text-sky">/ˈeləkwənt/</p>
                        <p class="mt-2 text-[14px] font-semibold text-brand">красноречивый</p>
                        <p class="mt-2 text-[13.5px] italic leading-relaxed text-ink/50">«She gave an eloquent speech.»</p>
                    </div>
                    <div data-reveal class="card-lift rounded-xl border border-line bg-armor2 p-5">
                        <p class="font-display text-[18px] font-bold text-ink">perseverance</p>
                        <p class="font-mono text-[13px] text-sky">/ˌpɜːsəˈvɪərəns/</p>
                        <p class="mt-2 text-[14px] font-semibold text-brand">упорство</p>
                        <p class="mt-2 text-[13.5px] italic leading-relaxed text-ink/50">«Success requires perseverance.»</p>
                    </div>
                </div>

                <div class="flip-card mx-auto h-56 w-72 cursor-pointer" id="homeFlashcard" data-reveal>
                    <div class="flip-card-inner h-full w-full">
                        <div class="flip-face flex h-full flex-col items-center justify-center rounded-2xl border border-line bg-armor2 p-6 text-center shadow-softLg">
                            <p class="text-[11px] font-bold uppercase tracking-widest text-ink/40">Слово</p>
                            <p class="mt-3 font-display text-[25px] font-extrabold text-ink">wanderlust</p>
                            <p class="mt-1 font-mono text-[13px] text-sky">/ˈwɒndəlʌst/</p>
                            <p class="mt-5 text-[12px] text-ink/40">Нажми, чтобы перевернуть</p>
                        </div>
                        <div class="flip-face flip-back flex h-full flex-col items-center justify-center rounded-2xl bg-brand p-6 text-center text-white shadow-softLg">
                            <p class="text-[11px] font-bold uppercase tracking-widest text-white/70">Перевод</p>
                            <p class="mt-3 font-display text-[23px] font-extrabold">страсть к путешествиям</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ===================== PHIL ===================== --}}
    <section class="py-20 sm:py-24">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div data-reveal class="grid items-center gap-10 rounded-2xl border border-line bg-armor2 p-8 shadow-soft sm:p-12 lg:grid-cols-2">
                <div>
                    <span class="inline-flex items-center gap-2 rounded-full bg-brand/10 px-3.5 py-1.5 text-[12.5px] font-semibold text-brand">AI-помощник</span>
                    <h2 class="mt-4 font-display text-[26px] font-extrabold tracking-tight text-ink sm:text-[32px]">Знакомься, это <span class="text-brand">Phil</span></h2>
                    <p class="mt-3 max-w-md text-[15.5px] leading-relaxed text-ink/60">Задай вопрос про грамматику, слово или произношение прямо на сайте — Phil ответит с примерами и объяснит так, чтобы точно запомнилось. Открыть его можно в любой момент — кнопка внизу справа.</p>
                </div>
                <div class="space-y-3 rounded-xl bg-surface2 p-5">
                    <div class="flex items-end gap-2">
                        <span class="grid h-7 w-7 shrink-0 place-items-center rounded-full bg-brand text-white">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10" /><path d="M8 14s1.5 2 4 2 4-2 4-2" /></svg>
                        </span>
                        <div class="max-w-[80%] rounded-xl rounded-bl-sm bg-armor2 px-3.5 py-2.5 text-[13.5px] leading-relaxed text-ink shadow-sm">В чём разница между «a few» и «few»?</div>
                    </div>
                    <div class="flex justify-end">
                        <div class="max-w-[80%] rounded-xl rounded-br-sm bg-brand px-3.5 py-2.5 text-[13.5px] leading-relaxed text-white shadow-sm">«A few» значит «несколько» — I have a few friends. А «few» значит «почти нет» — так что смысл противоположный.</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ===================== ДОСТИЖЕНИЯ (иллюстративно) ===================== --}}
    <section id="achievements" class="border-t border-line bg-surface2/50 py-20 sm:py-28">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="mx-auto max-w-2xl text-center" data-reveal>
                <h2 class="font-display text-[30px] font-extrabold tracking-tight text-ink sm:text-[36px]">Учёба, в которую хочется возвращаться</h2>
                <p class="mt-3 text-[16px] text-ink/60">Собирай награды за streak, пройденные темы и новые слова.</p>
            </div>

            <div class="mt-14 grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-5">
                @foreach ([
                    ['label' => 'Первый урок', 'icon' => '<path d="M22 10 12 5 2 10l10 5 10-5Z" /><path d="M6 12v5c0 1.5 3 3 6 3s6-1.5 6-3v-5" />'],
                    ['label' => 'Неделя подряд', 'icon' => '<path d="M12 2c1 3-2 4-2 7a4 4 0 0 0 8 0c0-1-.4-2-1-3 2 1 3 3.5 3 6a7 7 0 1 1-14 0c0-4 2-6 3-7 1-1 2-2 3-3Z" fill="currentColor" stroke="none" />'],
                    ['label' => '100 слов', 'icon' => '<path d="m5 8 6 6" /><path d="m4 14 6-6 2-3" /><path d="M2 5h12" /><path d="m22 22-5-10-5 10" /><path d="M14 18h6" />'],
                    ['label' => 'Тест на 100%', 'icon' => '<path d="m3 17 2 2 4-4" /><path d="M13 6h8M13 12h8M13 18h8" />'],
                    ['label' => 'Уровень A2', 'icon' => '<circle cx="12" cy="8" r="6" /><path d="M15.477 12.89 17 22l-5-3-5 3 1.523-9.11" />'],
                ] as $badge)
                    <div data-reveal class="flex flex-col items-center rounded-2xl border border-line bg-armor2 p-6 text-center">
                        <span class="grid h-14 w-14 place-items-center rounded-full bg-brand text-white">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">{!! $badge['icon'] !!}</svg>
                        </span>
                        <p class="mt-3 text-[13px] font-bold leading-tight text-ink">{{ $badge['label'] }}</p>
                    </div>
                @endforeach

                <div data-reveal class="flex flex-col items-center rounded-2xl border border-line bg-armor2 p-6 text-center opacity-60">
                    <span class="grid h-14 w-14 place-items-center rounded-full bg-surface2 text-ink/40">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="11" width="16" height="10" rx="2" /><path d="M8 11V7a4 4 0 0 1 8 0v4" /></svg>
                    </span>
                    <p class="mt-3 text-[13px] font-bold leading-tight text-ink/50">Мастер грамматики</p>
                </div>
            </div>
        </div>
    </section>

    {{-- ===================== CTA ===================== --}}
    <section class="py-20 sm:py-24">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div data-reveal class="relative overflow-hidden rounded-2xl bg-brand px-8 py-16 text-center sm:px-16">
                <h2 class="relative font-display text-[28px] font-extrabold text-white sm:text-[34px]">Готов заговорить свободно?</h2>
                <p class="relative mx-auto mt-3 max-w-md text-[15.5px] text-white/80">Присоединяйся бесплатно — первые уроки открыты для всех.</p>
                <a href="{{ route('register') }}" class="relative mt-7 inline-flex items-center gap-2 rounded-lg bg-white px-7 py-3.5 text-[15px] font-semibold text-brand shadow-xl transition hover:bg-white/90">
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
