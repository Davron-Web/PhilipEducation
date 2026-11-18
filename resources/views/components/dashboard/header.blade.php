{{-- Шапка дашборда: имя, уровень CEFR крупно, streak.

     Streak — НЕ отдельное поле в БД: считается на лету в
     DashboardController::currentStreak() по датам completed_at в
     user_progress (сколько дней подряд, включая сегодня/вчера, был
     завершён хотя бы один урок). Для дашборда одного пользователя это
     достаточно быстро и не требует миграции — так и сказано в ответе
     по задаче, а не молча решено «раз работает, значит поле есть».

     Классы acc-* — из-за того, что Tailwind не умеет накладывать альфа-
     канал на цвета проекта (они заданы через var() в tailwind.config.js,
     поэтому bg-brand/10, border-sun/30 и т.п. компилируются в сплошной
     непрозрачный цвет — проверено на собранном CSS). Старая версия этой
     шапки использовала именно такие классы для плашек XP/streak — раз
     переписываю файл всё равно, заменил на color-mix(), который эту
     проблему обходит (см. общий <style> в user/dashboard.blade.php). --}}
@props([
    'user' => null,
    'streak' => 0,
    'streakDays' => [],
    'title' => null,
    'nextTitle' => null,
    'xp' => 0,
])

@php
    $user = $user ?? auth()->user();
    $firstName = explode(' ', trim($user->name))[0] ?: $user->name;
@endphp

<div class="mb-8 flex flex-wrap items-end justify-between gap-4" data-reveal>
    <div class="flex flex-wrap items-center gap-4">
        <div>
            <h1 class="text-3xl font-extrabold text-ink sm:text-4xl">С возвращением, {{ $firstName }}!</h1>
            <p class="mt-1 text-ink/60">Продолжим заниматься английским — вот что у вас сейчас.</p>
        </div>

        {{-- Уровень CEFR крупно — часть приветствия, а не мелкая деталь. --}}
        <div class="flex items-center gap-3 rounded-2xl border border-line bg-armor2/70 px-4 py-2.5">
            <span class="text-3xl font-extrabold leading-none text-brand">{{ $user->level->code ?? '—' }}</span>
            <span class="text-xs font-semibold uppercase tracking-wide text-ink/50">
                {{ $user->level->name ?? 'Уровень не определён' }}
            </span>
        </div>
    </div>

    <div class="flex flex-wrap items-stretch gap-3">
        {{-- Опыт и звание --}}
        <div class="acc-tint acc-border rounded-2xl border px-4 py-3" style="--accent: var(--pe-brand); color: var(--pe-brand)">
            <div class="flex items-center gap-2">
                <span class="text-xl leading-none">{{ $title?->icon ?? '🌱' }}</span>
                <div class="leading-tight">
                    <p class="text-lg font-extrabold"><span data-counter data-target="{{ $xp }}">0</span></p>
                    <p class="acc-text-soft text-xs font-semibold uppercase tracking-wide">
                        XP · {{ $title?->name ?? 'Новичок' }}
                    </p>
                </div>
            </div>

            @if ($nextTitle)
                @php
                    $from = $title?->min_xp ?? 0;
                    $span = max(1, $nextTitle->min_xp - $from);
                    $done = min(100, round((($xp - $from) / $span) * 100));
                @endphp
                <div class="mt-2 w-40">
                    <div class="acc-bar-track h-1.5 overflow-hidden rounded-full">
                        <div class="acc-bar-fill h-full rounded-full" data-fill-width="{{ $done }}"></div>
                    </div>
                    <p class="acc-text-soft mt-1 text-[11px] font-semibold">
                        До «{{ $nextTitle->name }}» — {{ number_format($nextTitle->min_xp - $xp, 0, ',', ' ') }} XP
                    </p>
                </div>
            @else
                <p class="acc-text-soft mt-2 text-[11px] font-semibold">Высшее звание получено</p>
            @endif
        </div>

        {{-- Streak --}}
        <div class="acc-tint acc-border rounded-2xl border px-4 py-3" style="--accent: var(--pe-sun); color: var(--pe-sun)">
            <div class="flex items-center gap-2">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 2c1 3-2 4-2 7a4 4 0 0 0 8 0c0-1-.4-2-1-3 2 1 3 3.5 3 6a7 7 0 1 1-14 0c0-4 2-6 3-7 1-1 2-2 3-3Z" /></svg>
                <div class="leading-tight">
                    <p class="text-lg font-extrabold"><span data-counter data-target="{{ $streak }}">0</span></p>
                    <p class="acc-text-soft text-xs font-semibold uppercase tracking-wide">
                        {{ $streak === 1 ? 'день подряд' : 'дней подряд' }}
                    </p>
                </div>
            </div>
            <div class="mt-2 flex items-center gap-1">
                @foreach ($streakDays as $day)
                    <span
                        class="h-2.5 w-2.5 rounded-full {{ $day['active'] ? 'bg-sun' : '' }} {{ $day['isToday'] ? 'ring-2 ring-sun ring-offset-1 ring-offset-armor' : '' }}"
                        style="{{ $day['active'] ? '' : 'background-color: color-mix(in srgb, var(--pe-sun) 15%, transparent)' }}"
                        title="{{ $day['label'] }}"
                    ></span>
                @endforeach
            </div>
        </div>
    </div>
</div>
