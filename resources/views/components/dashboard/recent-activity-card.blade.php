{{-- Последние начисления опыта (XpEvent) — реальная лента событий, не
     выдуманная активность. Пустое состояние — у совсем нового ученика. --}}
@props(['events', 'delay' => 0])

<x-ui.card class="!p-6" :hover="true" style="transition-delay: {{ $delay }}ms">
    <div class="mb-4 flex items-center gap-2">
        <span class="acc-icon flex h-9 w-9 items-center justify-center rounded-xl" style="--accent: var(--pe-sky)">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 8v4l3 3M12 21a9 9 0 1 0 0-18 9 9 0 0 0 0 18Z" /></svg>
        </span>
        <h3 class="text-lg font-bold text-ink">Последняя активность</h3>
    </div>

    @if ($events->isNotEmpty())
        <ul class="space-y-3">
            @foreach ($events as $event)
                <li class="flex items-center justify-between gap-3 text-sm">
                    <span class="text-ink/70">{{ $event->label() }}</span>
                    <span class="flex shrink-0 items-center gap-2">
                        <span class="font-bold text-brand">+{{ $event->amount }} XP</span>
                        <span class="text-xs text-ink/40">{{ $event->created_at->diffForHumans() }}</span>
                    </span>
                </li>
            @endforeach
        </ul>
    @else
        <p class="text-sm text-ink/60">Как только вы пройдёте первый урок или тест — здесь появится история начислений опыта.</p>
    @endif
</x-ui.card>
