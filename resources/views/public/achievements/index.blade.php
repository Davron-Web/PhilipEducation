{{-- Прогресс: календарь активности (по XP за день) + достижения (у каждого
     своя иконка по смыслу — components/achievement-icon.blade.php). --}}
@extends('layouts.app')

@section('title', 'Прогресс')
@section('page_title', 'Достижения')
@section('meta_description', 'Ваш прогресс в изучении английского: активность по дням и достижения.')

@push('styles')
    <style>
        {{-- До сегодняшней сидевой задачи Tailwind не умел накладывать альфа-
             канал на цвета проекта (заданы через var() в tailwind.config.js)
             — bg-ink/50, text-ink/NN и подобные классы не попадали в бандл
             вообще, и здесь стоял самодельный набор .ti-*/.bi-*/.bo-* на
             color-mix(). Теперь цвета в tailwind.config.js заданы через
             rgb(var(--pe-X-rgb) / <alpha-value>) — обычные классы вида
             text-ink/50 работают нативно по всему сайту, костыль убран. --}}
        .heat-0 { background-color: color-mix(in srgb, var(--pe-ink) 6%, transparent); }
        .heat-1 { background-color: color-mix(in srgb, var(--pe-sky) 25%, transparent); }
        .heat-2 { background-color: color-mix(in srgb, var(--pe-sky) 55%, transparent); }
        .heat-3 { background-color: color-mix(in srgb, var(--pe-brand) 75%, transparent); }
        .heat-4 { background-color: var(--pe-brand); }

        {{-- Общий язык «получено» — одни и те же классы у достижений и у
             сертификатов (было два похожих, но не идентичных набора,
             свели в один). Состояние читается ТОЛЬКО яркостью/свечением —
             никаких замков и бейджей поверх иконки.
             Цвет — brand→sky, не тёплый sun/amber/жёлтый: сайт весь синий
             после отдельной задачи унификации палитры. Про «янтарную»
             рамку сертификата — решение ещё не принято, здесь тот же
             синий вариант, что и был. --}}
        .earned-icon {
            background: linear-gradient(135deg, var(--pe-brand), var(--pe-sky));
            box-shadow: 0 0 20px 2px color-mix(in srgb, var(--pe-brand) 45%, transparent), 0 8px 20px -6px var(--pe-brand);
        }
        .earned-card {
            border-color: color-mix(in srgb, var(--pe-brand) 30%, transparent);
            background: color-mix(in srgb, var(--pe-brand) 3%, var(--pe-armor2));
        }
        @media (prefers-reduced-motion: no-preference) {
            .earned-icon { animation: earned-pulse 2.5s ease-in-out infinite; }
        }
        @keyframes earned-pulse {
            0%, 100% { box-shadow: 0 0 20px 2px color-mix(in srgb, var(--pe-brand) 45%, transparent), 0 8px 20px -6px var(--pe-brand); }
            50% { box-shadow: 0 0 28px 6px color-mix(in srgb, var(--pe-brand) 55%, transparent), 0 8px 20px -6px var(--pe-brand); }
        }
    </style>
@endpush

@section('content')
    <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">

        {{-- ===== Шапка ===== --}}
        <div class="mb-10 flex flex-col items-start justify-between gap-4 border-b border-ink/10 pb-6 sm:flex-row sm:items-end" data-reveal>
            <div>
                <h1 class="text-3xl font-extrabold text-ink sm:text-4xl">Ваш прогресс</h1>
                <p class="mt-2 text-ink/60">Продолжайте в том же духе — у вас отлично получается!</p>
            </div>
            <div class="text-right">
                <span class="text-2xl font-extrabold text-brand">{{ $earnedCount }}</span>
                <span class="text-sm font-semibold text-ink/50"> / {{ $totalCount }}</span>
                <p class="text-xs font-medium text-ink/40">достижений получено</p>
            </div>
        </div>

        {{-- ===== Календарь активности: 12 недель × 7 дней, по XP за день =====
             Слева сетка+легенда, справа три показателя — все три взяты из
             того же прохода по дням, что строит саму сетку (см.
             AchievementController::activityHeatmap()), ничего не досчитано
             отдельным запросом. На мобильных — одна колонка (grid без lg:
             по умолчанию), статистика уходит под heatmap сама собой. --}}
        <x-ui.card :hover="false" class="mb-10 p-6" data-reveal>
            <h2 class="mb-6 text-sm font-bold uppercase tracking-wide text-ink/50">Активность за 12 недель</h2>

            <div class="grid gap-8 lg:grid-cols-[auto_1fr] lg:items-start">
                <div>
                    <div class="overflow-x-auto pb-4">
                        <div class="grid w-max grid-flow-col grid-rows-7 gap-[3px]">
                            @foreach ($heatmap['cells'] as $day)
                                <div
                                    class="h-3.5 w-3.5 rounded-sm heat-{{ $day['level'] }} cursor-pointer transition-transform duration-150 hover:scale-125 sm:h-4 sm:w-4"
                                    title="{{ \Illuminate\Support\Carbon::parse($day['date'])->translatedFormat('d M') }} — {{ $day['xp'] }} XP"
                                ></div>
                            @endforeach
                        </div>
                    </div>

                    <div class="flex items-center gap-2 text-xs font-medium text-ink/40">
                        <span>Меньше</span>
                        <div class="flex gap-1">
                            @for ($level = 0; $level <= 4; $level++)
                                <div class="h-3 w-3 rounded-sm heat-{{ $level }}"></div>
                            @endfor
                        </div>
                        <span>Больше</span>
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-6 border-t border-ink/10 pt-6 lg:border-t-0 lg:border-l lg:pl-8 lg:pt-0">
                    <div>
                        <p class="text-3xl font-extrabold text-ink">{{ $heatmap['streak'] }}</p>
                        <p class="mt-1 text-xs font-semibold uppercase tracking-wide text-ink/50">
                            {{ $heatmap['streak'] === 1 ? 'день подряд' : 'дней подряд' }}
                        </p>
                        <p class="text-ink/40 text-xs">Текущая серия</p>
                    </div>
                    <div>
                        <p class="text-3xl font-extrabold text-ink">{{ number_format($heatmap['total_xp'], 0, ',', ' ') }}</p>
                        <p class="mt-1 text-xs font-semibold uppercase tracking-wide text-ink/50">XP</p>
                        <p class="text-ink/40 text-xs">Всего за 12 недель</p>
                    </div>
                    <div>
                        <p class="text-3xl font-extrabold text-ink">{{ $heatmap['active_days'] }}<span class="text-ink/40 text-lg font-semibold"> / 84</span></p>
                        <p class="mt-1 text-xs font-semibold uppercase tracking-wide text-ink/50">дней</p>
                        <p class="text-ink/40 text-xs">Активных дней</p>
                    </div>
                </div>
            </div>
        </x-ui.card>

        {{-- ===== Достижения ===== --}}
        <div class="mb-6 flex items-baseline justify-between" data-reveal>
            <h2 class="text-xl font-extrabold text-ink">Достижения</h2>
        </div>

        @if ($achievements->isEmpty())
            <x-ui.card :hover="false" class="py-16 text-center">
                <p class="text-lg font-semibold text-ink">Достижения ещё не настроены</p>
                <p class="mt-1 text-ink/50">Загляните позже.</p>
            </x-ui.card>
        @else
            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                @foreach ($achievements as $item)
                    @php $achievement = $item['achievement']; $isEarned = $item['is_earned']; @endphp

                    <div
                        class="group flex h-full flex-col items-center rounded-2xl border bg-armor2 p-6 text-center shadow-lg backdrop-blur-xl transition-all duration-300 ease-out
                        {{ $isEarned ? 'earned-card hover:-translate-y-1 hover:shadow-2xl' : 'border-dashed border-ink/20 bg-ink/5' }}"
                        data-reveal
                    >
                        {{-- Иконка: своя для каждого достижения (components/
                             achievement-icon.blade.php). Состояние читается
                             только яркостью — никаких замков/бейджей поверх:
                             полученная — цветная на градиенте с мягким
                             свечением (и лёгким pulse), незаработанная — та
                             же иконка, но серая и приглушённая, без свечения. --}}
                        <span class="mb-4 flex h-16 w-16 items-center justify-center rounded-full transition-transform duration-300 group-hover:scale-110 {{ $isEarned ? 'earned-icon text-white' : 'bg-ink/10 text-ink/40 opacity-40 grayscale' }}">
                            <x-achievement-icon :icon="$achievement->icon" :size="28" />
                        </span>

                        {{-- Текст --}}
                        <h3 class="text-base font-bold text-ink">{{ $achievement->title }}</h3>
                        <p class="mt-2 text-sm leading-relaxed text-ink/50">{{ $achievement->description }}</p>

                        @if ($item['progress'])
                            <div class="mt-3 w-full">
                                <div class="h-1.5 overflow-hidden rounded-full bg-ink/10">
                                    <div class="h-full rounded-full bg-brand" data-fill-width="{{ $item['progress']['percent'] }}"></div>
                                </div>
                                <p class="mt-1.5 text-xs font-semibold text-ink/40">
                                    {{ $item['progress']['current'] }} из {{ $item['progress']['target'] }} {{ $item['progress']['label'] }}
                                </p>
                            </div>
                        @endif

                        {{-- Нижняя часть (прижата к низу благодаря mt-auto) --}}
                        <div class="mt-auto flex w-full flex-col items-center gap-2 pt-4">
                            <x-ui.badge :variant="$isEarned ? 'accent' : 'neutral'" class="px-3 py-1">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor" class="mr-1"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" /></svg>
                                {{ $achievement->points }} очков
                            </x-ui.badge>

                            @if ($isEarned && $item['earned_at'])
                                <span class="flex items-center gap-1 text-xs text-ink/40">
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="opacity-60">
                                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                        <line x1="16" y1="2" x2="16" y2="6"></line>
                                        <line x1="8" y1="2" x2="8" y2="6"></line>
                                        <line x1="3" y1="10" x2="21" y2="10"></line>
                                    </svg>
                                    {{ \Illuminate\Support\Carbon::parse($item['earned_at'])->translatedFormat('d M Y') }}
                                </span>
                            @else
                                <span class="text-xs text-ink/30">Ещё не получено</span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        {{-- ===== Сертификаты уровня =====
             Автовыдача сюда ещё не подключена (следующий этап) — данные
             ниже честно показывают текущий прогресс по трём условиям
             отдельно, ни один сертификат пока не может быть выдан
             автоматически. is_available=false (нет опубликованных уроков
             или тестов уровня — сейчас это только C2) даёт карточку
             «Скоро» общим правилом, не проверкой конкретного кода. --}}
        <div class="mb-6 mt-12 flex items-center gap-2" data-reveal>
            <span class="flex h-9 w-9 items-center justify-center rounded-xl" style="background-color: color-mix(in srgb, var(--pe-sky) 16%, transparent); color: var(--pe-sky)">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M8 21h8M12 17v4M7 4h10v4a5 5 0 0 1-10 0V4Z" /><path d="M7 6H4a1 1 0 0 0-1 1c0 2.5 1.8 4.5 4.2 4.9M17 6h3a1 1 0 0 1 1 1c0 2.5-1.8 4.5-4.2 4.9" /></svg>
            </span>
            <h2 class="text-xl font-extrabold text-ink">Сертификаты</h2>
        </div>

        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
            @foreach ($certificateLevels as $cert)
                <div
                    class="flex h-full flex-col overflow-hidden rounded-2xl border shadow-lg backdrop-blur-xl {{ $cert['is_earned'] ? 'earned-card hover:-translate-y-1 hover:shadow-2xl transition-transform duration-300' : 'border-dashed border-ink/20 bg-ink/5' }} {{ ! $cert['is_available'] ? 'opacity-70' : '' }}"
                    data-reveal
                >
                    {{-- Превью самого сертификата (canonical-макет —
                         resources/views/certificates/pdf.blade.php)
                         заменяет прежнюю круглую иконку-трофей: состояние
                         читается яркостью самого диплома (цветной со
                         свечением / приглушённый с пеленой), как и у
                         достижений выше. Для «Скоро» превью не рисуем —
                         показывать нечего, там остаётся прежняя иконка. --}}
                    @if ($cert['is_available'])
                        <x-certificate-preview
                            :level-code="$cert['code']"
                            :level-name="$cert['name']"
                            :earned="$cert['is_earned']"
                            :recipient-name="$cert['is_earned'] ? auth()->user()->name : null"
                            :certificate-number="$cert['is_earned'] ? $cert['certificate']->certificate_number : null"
                            :issued-at="$cert['is_earned'] ? $cert['certificate']->issued_at : null"
                        />
                    @endif

                    <div class="flex flex-1 flex-col p-6 text-center">
                        @unless ($cert['is_available'])
                            <span class="mx-auto mb-3 flex h-14 w-14 items-center justify-center rounded-full bg-ink/10 text-ink/40 opacity-40 grayscale">
                                <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M8 21h8M12 17v4M7 4h10v4a5 5 0 0 1-10 0V4Z" /><path d="M7 6H4a1 1 0 0 0-1 1c0 2.5 1.8 4.5 4.2 4.9M17 6h3a1 1 0 0 1 1 1c0 2.5-1.8 4.5-4.2 4.9" /></svg>
                            </span>
                        @endunless

                        <h3 class="text-lg font-bold text-ink">Английский уровня {{ $cert['code'] }}</h3>
                        <p class="mt-1 text-sm text-ink/50">{{ $cert['name'] }}</p>

                        @if (! $cert['is_available'])
                            <x-ui.badge variant="neutral" class="mx-auto mt-3">Скоро</x-ui.badge>
                            <p class="mt-3 text-xs text-ink/40">Уровень пока не наполнен уроками и тестами — сертификат появится, когда будет что подтверждать.</p>
                        @elseif ($cert['is_earned'])
                            @php $certificate = $cert['certificate']; @endphp
                            <p class="mt-2 text-xs text-ink/40">
                                Выдан {{ \Illuminate\Support\Carbon::parse($certificate->issued_at)->translatedFormat('d M Y') }}
                                @if ($certificate->certificate_number)
                                    · {{ $certificate->certificate_number }}
                                @endif
                            </p>

                            {{-- Скачивание PDF и публичная проверка — следующие этапы,
                                 кнопки пока намеренно неактивны, а не ведут в никуда. --}}
                            <div class="mt-auto flex gap-2 pt-5">
                                <x-ui.button variant="outline" class="flex-1" disabled>Download PDF</x-ui.button>
                                <x-ui.button variant="outline" class="flex-1" disabled>Share</x-ui.button>
                            </div>
                        @else
                            <div class="mt-5 space-y-4 text-left">
                                <div>
                                    <div class="flex items-center justify-between text-xs font-semibold text-ink/50">
                                        <span class="{{ $cert['lessons']['complete'] ? 'text-brand' : '' }}">Уроки{{ $cert['lessons']['complete'] ? ' ✓' : '' }}</span>
                                        <span>{{ $cert['lessons']['current'] }} из {{ $cert['lessons']['target'] }}</span>
                                    </div>
                                    <div class="mt-1 h-1.5 overflow-hidden rounded-full bg-ink/10">
                                        <div class="h-full rounded-full bg-brand" data-fill-width="{{ $cert['lessons']['percent'] }}"></div>
                                    </div>
                                </div>

                                <div>
                                    <div class="flex items-center justify-between text-xs font-semibold text-ink/50">
                                        <span class="{{ $cert['tests']['complete'] ? 'text-brand' : '' }}">Тесты{{ $cert['tests']['complete'] ? ' ✓' : '' }}</span>
                                        <span>{{ $cert['tests']['current'] }} из {{ $cert['tests']['target'] }}, средний {{ $cert['tests']['avg_score'] }}% (нужно {{ $cert['tests']['required_score'] }}%)</span>
                                    </div>
                                    <div class="mt-1 h-1.5 overflow-hidden rounded-full bg-ink/10">
                                        <div class="h-full rounded-full bg-brand" data-fill-width="{{ $cert['tests']['percent'] }}"></div>
                                    </div>
                                </div>

                                <div>
                                    <div class="flex items-center justify-between text-xs font-semibold text-ink/50">
                                        <span class="{{ $cert['words']['complete'] ? 'text-brand' : '' }}">Слова{{ $cert['words']['complete'] ? ' ✓' : '' }}</span>
                                        <span>{{ $cert['words']['current'] }} из {{ $cert['words']['target'] }}</span>
                                    </div>
                                    <div class="mt-1 h-1.5 overflow-hidden rounded-full bg-ink/10">
                                        <div class="h-full rounded-full bg-brand" data-fill-width="{{ $cert['words']['percent'] }}"></div>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-auto pt-5">
                                <span class="text-xs font-semibold text-ink/30">Ещё не получен</span>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endsection
