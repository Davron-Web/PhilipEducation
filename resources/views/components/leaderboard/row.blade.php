{{--
    Одна строка списка рейтинга (места 4+, и закреплённая строка «моё место»).
    Имя — не ссылка: UserPolicy::view() и так разрешает смотреть только свой
    профиль или чужой — админу; чужих профилей на сайте не открыть, рейтинг
    это не меняет.
--}}
@props(['rank', 'user', 'points', 'change' => null, 'highlight' => false])

<div class="flex items-center gap-3 rounded-2xl px-3 py-2.5 {{ $highlight ? 'bg-brand/10 ring-1 ring-inset ring-brand/30' : '' }}">
    <span class="w-7 shrink-0 text-center text-sm font-bold text-ink/40">{{ $rank }}</span>

    <span class="relative flex h-9 w-9 shrink-0 items-center justify-center overflow-hidden rounded-full bg-gradient-to-br from-brand to-sky text-xs font-extrabold text-white">
        @if ($user->avatar_url)
            <img src="{{ $user->avatar_url }}" alt="" class="h-full w-full object-cover">
        @else
            {{ $user->initial }}
        @endif
    </span>

    <span class="min-w-0 flex-1 truncate text-sm font-semibold text-ink">
        {{ $user->name }}
    </span>

    <span class="shrink-0 text-sm font-bold text-ink">{{ number_format($points, 0, '', ' ') }}</span>

    <span class="flex w-12 shrink-0 items-center justify-end gap-0.5 text-xs font-bold">
        @if ($change === null)
            <span class="text-ink/30">new</span>
        @elseif ($change > 0)
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" class="text-green-600 dark:text-green-400"><path d="M12 19V5M5 12l7-7 7 7" /></svg>
            <span class="text-green-600 dark:text-green-400">{{ $change }}</span>
        @elseif ($change < 0)
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" class="text-red-500 dark:text-red-400"><path d="M12 5v14M5 12l7 7 7-7" /></svg>
            <span class="text-red-500 dark:text-red-400">{{ abs($change) }}</span>
        @else
            <span class="text-ink/40">=</span>
        @endif
    </span>
</div>
