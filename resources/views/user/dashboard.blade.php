{{-- Личный кабинет — собран из компонентов resources/views/components/dashboard/*,
     все запросы к БД сделаны в DashboardController::index(), здесь только
     раскладка и стаггер-задержки появления. Старая цельная вьюха — в
     backup/resources/views/user/dashboard.blade.php. --}}
@extends('layouts.app')

@section('title', 'Личный кабинет')
@section('meta_description', 'Личный кабинет ученика Philip Education — прогресс, streak и задание дня.')

@push('styles')
    <style>
        /* Tailwind не умеет накладывать альфа-канал на цвета проекта — они
           заданы через var() в tailwind.config.js, поэтому bg-brand/10 и
           подобные классы компилируются в сплошной непрозрачный цвет
           (проверено на собранном CSS в рамках более ранней задачи).
           color-mix() берёт уже посчитанное браузером значение переменной,
           минуя сборщик Tailwind — этим набором классов пользуются все
           компоненты dashboard/*, задавая --accent инлайн-стилем. */
        .acc-tint { background-color: color-mix(in srgb, var(--accent) 8%, transparent); transition: background-color .2s ease; }
        .acc-tint:hover { background-color: color-mix(in srgb, var(--accent) 14%, transparent); }
        .acc-icon { background-color: color-mix(in srgb, var(--accent) 16%, transparent); color: var(--accent); }
        .acc-border { border-color: color-mix(in srgb, var(--accent) 30%, transparent); }
        .acc-text-soft { color: color-mix(in srgb, var(--accent) 70%, transparent); }
        .acc-ring-bg { stroke: color-mix(in srgb, var(--accent) 16%, transparent); }
        .acc-ring-fg { stroke: var(--accent); }
        .acc-bar-track { background-color: color-mix(in srgb, var(--accent) 20%, transparent); }
        .acc-bar-fill { background-color: var(--accent); }

        /* Подъём карточки на hover — 250ms по ТЗ, а не общесайтовые 350ms
           у .card-lift (не трогаем сам класс — он используется по всему
           сайту, переопределяем длительность только в рамках дашборда). */
        .dashboard-page .card-lift { transition-duration: .25s; }

        /* Рамка/свечение у выделенного тарифа (PRO) — через inline-цвет,
           не классом Tailwind: два разных класса на border-color иначе
           конкурируют по порядку в СГЕНЕРИРОВАННОМ Tailwind-файле, а не по
           порядку в HTML, и исход непредсказуем (та же причина, по которой
           весь дашборд уже использует --accent + color-mix()). */
        .plan-card-highlight {
            border-color: var(--pe-sky) !important;
            box-shadow: 0 0 0 1px var(--pe-sky), 0 25px 50px -20px color-mix(in srgb, var(--pe-sky) 45%, transparent);
        }
    </style>
@endpush

@section('content')
    <div class="dashboard-page mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">

        {{-- Голосовое приветствие Phil --}}
        <x-voice-greeting :greeting="$philGreeting" />

        <x-dashboard.header
            :streak="$stats['streak']"
            :streak-days="$streakDays"
            :title="$title"
            :next-title="$nextTitle"
            :xp="$stats['xp']"
        />

        {{-- Статистика: 4 плитки, появляются с шагом 60мс. --}}
        <div class="mb-6 grid grid-cols-2 gap-4 sm:grid-cols-4">
            <x-dashboard.stat-card
                label="Слов выучено" :value="$stats['words_learned']" accent="brand"
                icon="M4 19.5A2.5 2.5 0 0 1 6.5 17H20M4 19.5A2.5 2.5 0 0 0 6.5 22H20V4H6.5A2.5 2.5 0 0 0 4 6.5v13Z"
                :progress="$stats['words_progress']" :delay="0"
            />
            <x-dashboard.stat-card
                label="Уроков пройдено" :value="$stats['lessons_completed']" accent="sky"
                icon="M12 20h9M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4Z"
                :progress="$stats['lessons_progress']" :delay="60"
            />
            <x-dashboard.stat-card
                label="Упражнений решено" :value="$stats['exercises_solved']" accent="skylight"
                icon="M9 11l3 3L22 4M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"
                :progress="null" :delay="120"
            />
            <x-dashboard.stat-card
                label="Тестов пройдено" :value="$stats['tests_passed']" accent="sun"
                icon="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2M9 5a2 2 0 0 0 2 2h2a2 2 0 0 0 2-2M9 5a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2M9 14l2 2 4-4"
                :progress="$stats['tests_progress']" :delay="180"
            />
        </div>

        {{-- Карточки на всю ширину --}}
        <div class="mb-6">
            {{-- Продолжить обучение (на всю ширину) --}}
            <x-dashboard.continue-lesson-card :lesson="$continueLesson" :progress="$continueLessonProgress" :delay="0" />
        </div>

        <div class="mb-6">
            {{-- Разделы (на всю ширину) --}}
            <x-dashboard.quick-links-card :delay="60" />
        </div>

        {{-- Остальные карточки в сетке 3 колонки (теперь 6 карточек: 3+3) --}}
        <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3 mb-10">
            <x-dashboard.level-card :delay="120" />
            <x-dashboard.word-of-day-card :word="$wordOfDay" :delay="180" />
            <x-dashboard.phil-card :delay="240" />
            <x-dashboard.achievements-card
                :achievements="$recentAchievements"
                :earned-count="$stats['achievements_count']"
                :total-count="$stats['achievements_total']"
                :delay="300"
            />
            <x-dashboard.daily-exercise-card :exercise="$dailyExercise" :delay="360" />

            {{-- Последняя активность (вместо weekly-progress-card) --}}
            <x-dashboard.recent-activity-card :events="$recentXp" :delay="420" />
        </div>

        <x-dashboard.pricing-section :plans="$plans" />
    </div>
@endsection
