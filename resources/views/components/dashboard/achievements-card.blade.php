{{-- Последние 3-4 полученных достижения + счётчик «X из Y открыто».
     Без выдуманного прогресса по недостающим достижениям — это отдельная
     логика на странице /achievements, здесь только витрина уже
     заработанного. Пустое состояние — когда earned=0, но total всё равно
     показываем (мотивирует, а не выглядит поломанным). --}}
@props([
    'achievements',
    'earnedCount' => 0,
    'totalCount' => 0,
    'delay' => 0,
])

<x-ui.card class="!p-6" :hover="true" style="transition-delay: {{ $delay }}ms">
    <div class="mb-4 flex items-center justify-between">
        <h3 class="text-sm font-bold uppercase tracking-wide text-ink/50">Достижения</h3>
        <a href="{{ route('achievements.index') }}" class="text-xs font-bold uppercase tracking-wide text-brand hover:underline">
            {{ $earnedCount }} из {{ $totalCount }} →
        </a>
    </div>

    @if ($achievements->isEmpty())
        <p class="text-sm text-ink/60">Пока нет открытых достижений — пройдите первый урок или тест, и здесь появится первая награда.</p>
    @else
        <div class="grid grid-cols-2 gap-4 sm:grid-cols-4">
            @foreach ($achievements as $achievement)
                <a href="{{ route('achievements.index') }}" class="group flex flex-col items-center gap-1.5 text-center" title="{{ $achievement->title }}">
                    <span class="flex h-14 w-14 items-center justify-center rounded-full bg-gradient-to-br from-sun to-amber-500 shadow-lg shadow-sun/40">
                        <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="#78350F" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M8 21h8M12 17v4M7 4h10v4a5 5 0 0 1-10 0V4Z" /><path d="M7 6H4a1 1 0 0 0-1 1c0 2.5 1.8 4.5 4.2 4.9M17 6h3a1 1 0 0 1 1 1c0 2.5-1.8 4.5-4.2 4.9" /></svg>
                    </span>
                    <span class="line-clamp-2 text-[11px] font-semibold leading-tight text-ink/60 group-hover:text-brand">{{ $achievement->title }}</span>
                </a>
            @endforeach
        </div>
    @endif
</x-ui.card>
