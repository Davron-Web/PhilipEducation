@extends('layouts.app')

@section('title', 'Philip Education')
@section('page_title', 'Учите английский язык')
@section('meta_description', 'Philip Education — платформа для изучения английского языка: уроки по уровням A1–C1, живой словарь, грамматика, тесты и ИИ-помощник Phil.')

@section('content')

    @php
        // Реальные цифры для блока статистики — без похода в контроллер,
        // т.к. страница не завязана ни на какой существующий эндпоинт.
        $lessonsCount = \App\Models\Content\Lesson::where('is_published', true)->count();
        $wordsCount = \App\Models\Vocabulary\Word::count();
        $usersCount = \App\Models\User::count();

        $levels = [
            ['code' => 'A1', 'title' => 'Начальный', 'desc' => 'Первые слова и фразы: приветствия, числа, простые предложения о себе.'],
            ['code' => 'A2', 'title' => 'Элементарный', 'desc' => 'Повседневные темы: покупки, семья, работа, простые диалоги.'],
            ['code' => 'B1', 'title' => 'Средний', 'desc' => 'Свободное общение на знакомые темы, чтение несложных текстов.'],
            ['code' => 'B2', 'title' => 'Выше среднего', 'desc' => 'Уверенная речь, аргументация мнения, понимание сложных текстов.'],
            ['code' => 'C1', 'title' => 'Продвинутый', 'desc' => 'Свободное владение языком в учёбе, работе и повседневной жизни.'],
        ];

        $testimonials = [
            ['name' => 'Алина', 'level' => 'B1', 'text' => 'За три месяца подтянула грамматику, которую не могла понять годами. Объяснения простые, с примерами.'],
            ['name' => 'Тимур', 'level' => 'A2', 'text' => 'Карточки слов с произношением — то, чего мне не хватало. Учу по 10 минут в день и реально запоминаю.'],
            ['name' => 'Мадина', 'level' => 'B2', 'text' => 'Нравится, что виден прогресс по каждому навыку отдельно. Понятно, над чем ещё работать.'],
            ['name' => 'Давид', 'level' => 'A1', 'text' => 'Начинал с нуля, было страшно. Уроки маленькие и понятные, не пугают объёмом.'],
            ['name' => 'Сабина', 'level' => 'C1', 'text' => 'Использую платформу для поддержания уровня — тесты и грамматика продвинутого уровня реально сложные, в хорошем смысле.'],
        ];
    @endphp

    {{-- ===================== ОБЛОЖКА ===================== --}}
    <section class="relative overflow-hidden px-4 pb-24 pt-16 sm:px-6 sm:pt-24 lg:px-8">
        <div
            data-parallax="0.15"
            class="pointer-events-none absolute -right-16 top-10 hidden h-72 w-72 rounded-full bg-gradient-to-br from-sun/40 to-sky/30 blur-2xl lg:block"
            aria-hidden="true"
        ></div>

        <div class="mx-auto max-w-4xl text-center">
            <div data-reveal class="mb-6 inline-flex items-center gap-2 rounded-full border border-brand/15 bg-white/70 px-4 py-1.5 text-sm font-semibold text-brand shadow-sm backdrop-blur">
                <span aria-hidden="true">🚀</span> Уже {{ number_format($usersCount) }}+ учеников с нами
            </div>

            <h1 data-reveal class="text-5xl font-black leading-[1.08] tracking-tight text-ink sm:text-6xl lg:text-7xl">
                Учите английский
                <span class="bg-gradient-to-r from-brand to-sky bg-clip-text text-transparent">по-настоящему</span>
            </h1>

            <p data-reveal class="mx-auto mt-6 max-w-2xl text-lg leading-relaxed text-ink/60 sm:text-xl">
                Уроки, грамматика, живой словарь с произношением и тесты — всё в одном месте, с понятным прогрессом на каждом шаге.
            </p>

            <div data-reveal class="mt-10 flex flex-col items-center justify-center gap-3 sm:flex-row">
                <x-ui.button href="{{ route('register') }}" variant="primary" size="lg">
                    Начать бесплатно
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="M5 12h14M13 6l6 6-6 6" /></svg>
                </x-ui.button>
                <x-ui.button href="#how-it-works" variant="outline" size="lg">
                    Как это работает
                </x-ui.button>
            </div>
        </div>
    </section>

    {{-- ===================== ПОЧЕМУ МЫ ===================== --}}
    <section id="how-it-works" class="px-4 py-20 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-6xl">
            <div data-reveal class="mx-auto mb-14 max-w-2xl text-center">
                <h2 class="text-3xl font-extrabold text-ink sm:text-4xl">Почему Philip Education</h2>
                <p class="mt-3 text-ink/60">Всё, что нужно для системного изучения языка — без хаоса из десятка разных приложений.</p>
            </div>

            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                @php
                    $features = [
                        ['icon' => '📚', 'title' => 'Уроки по уровням', 'desc' => 'От A1 до C1 — структурированный путь, а не хаотичный набор тем.'],
                        ['icon' => '🗣️', 'title' => 'Живой словарь', 'desc' => 'Карточки слов с произношением и переворотом — учите на слух и зрительно.'],
                        ['icon' => '🤖', 'title' => 'ИИ-помощник Phil', 'desc' => 'Задайте вопрос про грамматику в любой момент — ответит понятно и с примерами.'],
                        ['icon' => '📈', 'title' => 'Наглядный прогресс', 'desc' => 'Видите рост по чтению, письму, аудированию и словарному запасу отдельно.'],
                    ];
                @endphp

                @foreach ($features as $feature)
                    <x-ui.card>
                        <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-2xl bg-gradient-to-br from-brand/10 to-sky/10 text-2xl">
                            {{ $feature['icon'] }}
                        </div>
                        <h3 class="mb-2 text-lg font-bold text-ink">{{ $feature['title'] }}</h3>
                        <p class="text-sm leading-relaxed text-ink/60">{{ $feature['desc'] }}</p>
                    </x-ui.card>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ===================== УРОВНИ ===================== --}}
    <section class="px-4 py-20 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-6xl">
            <div data-reveal class="mx-auto mb-14 max-w-2xl text-center">
                <h2 class="text-3xl font-extrabold text-ink sm:text-4xl">От новичка до профи</h2>
                <p class="mt-3 text-ink/60">Пять уровней по системе CEFR — начните там, где вам комфортно.</p>
            </div>

            <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-5">
                @foreach ($levels as $level)
                    <x-ui.card :hover="true">
                        <x-ui.badge variant="level" :level="$level['code']" class="mb-4" />
                        <h3 class="mb-1.5 text-base font-bold text-ink">{{ $level['title'] }}</h3>
                        <p class="text-sm leading-relaxed text-ink/60">{{ $level['desc'] }}</p>
                    </x-ui.card>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ===================== ОТЗЫВЫ ===================== --}}
    <section
        x-data="{
            scrollBy(dir) {
                this.$refs.track.scrollBy({ left: dir * 340, behavior: 'smooth' });
            },
        }"
        class="px-4 py-20 sm:px-6 lg:px-8"
    >
        <div class="mx-auto max-w-6xl">
            <div data-reveal class="mb-10 flex flex-wrap items-end justify-between gap-4">
                <div>
                    <h2 class="text-3xl font-extrabold text-ink sm:text-4xl">Что говорят ученики</h2>
                    <p class="mt-3 text-ink/60">Реальный опыт людей, которые уже занимаются на платформе.</p>
                </div>
                <div class="flex gap-2">
                    <button
                        type="button"
                        @click="scrollBy(-1)"
                        class="flex h-11 w-11 items-center justify-center rounded-full border border-ink/10 bg-white text-ink/60 transition hover:border-brand hover:text-brand"
                        aria-label="Предыдущие отзывы"
                    >
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="m15 6-6 6 6 6" /></svg>
                    </button>
                    <button
                        type="button"
                        @click="scrollBy(1)"
                        class="flex h-11 w-11 items-center justify-center rounded-full border border-ink/10 bg-white text-ink/60 transition hover:border-brand hover:text-brand"
                        aria-label="Следующие отзывы"
                    >
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="m9 6 6 6-6 6" /></svg>
                    </button>
                </div>
            </div>

            <div x-ref="track" class="flex snap-x snap-mandatory gap-5 overflow-x-auto pb-4" style="scrollbar-width: none;">
                @foreach ($testimonials as $t)
                    <div class="w-80 shrink-0 snap-start">
                        <x-ui.card :hover="false" class="h-full">
                            <div class="mb-4 flex items-center gap-3">
                                <span class="flex h-11 w-11 items-center justify-center rounded-full bg-gradient-to-br from-brand to-sky text-sm font-bold text-white">
                                    {{ mb_substr($t['name'], 0, 1) }}
                                </span>
                                <div>
                                    <p class="font-bold text-ink">{{ $t['name'] }}</p>
                                    <x-ui.badge variant="level" :level="$t['level']" />
                                </div>
                            </div>
                            <p class="text-sm leading-relaxed text-ink/70">&laquo;{{ $t['text'] }}&raquo;</p>
                        </x-ui.card>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ===================== СТАТИСТИКА ===================== --}}
    <section class="px-4 py-16 sm:px-6 lg:px-8">
        <div data-reveal class="mx-auto max-w-5xl rounded-3xl border border-white/60 bg-gradient-to-br from-ink via-brand to-sky p-10 text-center text-white shadow-2xl shadow-brand/20 sm:p-14">
            <div class="grid gap-8 sm:grid-cols-3">
                <div>
                    <div class="text-4xl font-black sm:text-5xl">
                        <span data-counter data-target="{{ $lessonsCount }}">0</span>+
                    </div>
                    <p class="mt-2 text-sm font-medium text-white/70">уроков</p>
                </div>
                <div>
                    <div class="text-4xl font-black sm:text-5xl">
                        <span data-counter data-target="{{ $wordsCount }}">0</span>+
                    </div>
                    <p class="mt-2 text-sm font-medium text-white/70">слов в словаре</p>
                </div>
                <div>
                    <div class="text-4xl font-black sm:text-5xl">
                        <span data-counter data-target="{{ $usersCount }}">0</span>+
                    </div>
                    <p class="mt-2 text-sm font-medium text-white/70">учеников</p>
                </div>
            </div>
        </div>
    </section>

    {{-- ===================== ПРИЗЫВ К РЕГИСТРАЦИИ ===================== --}}
    <section class="px-4 py-20 sm:px-6 lg:px-8">
        <div data-reveal class="mx-auto max-w-3xl text-center">
            <h2 class="text-3xl font-extrabold text-ink sm:text-4xl">Готовы начать?</h2>
            <p class="mx-auto mt-3 max-w-xl text-ink/60">Регистрация бесплатна и занимает меньше минуты. Первый урок — уже через 30 секунд после входа.</p>
            <div class="mt-8">
                <x-ui.button href="{{ route('register') }}" variant="accent" size="lg">
                    Начать бесплатно
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="M5 12h14M13 6l6 6-6 6" /></svg>
                </x-ui.button>
            </div>
        </div>
    </section>

@endsection
