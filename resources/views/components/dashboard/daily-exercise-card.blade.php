{{-- Задание дня — первое упражнение, на которое ученик ещё не отвечал
     сегодня (DashboardController: whereDoesntHave userAnswers today).
     Пустое состояние — все доступные упражнения на сегодня уже сделаны. --}}
@props(['exercise' => null, 'delay' => 0])

<x-ui.card class="!p-6" :hover="true" style="transition-delay: {{ $delay }}ms">
    <div class="mb-4 flex items-center gap-2">
        <span class="acc-icon flex h-9 w-9 items-center justify-center rounded-xl" style="--accent: var(--pe-sun)">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2v2M12 20v2M4.9 4.9l1.4 1.4M17.7 17.7l1.4 1.4M2 12h2M20 12h2M4.9 19.1l1.4-1.4M17.7 6.3l1.4-1.4" /><circle cx="12" cy="12" r="4" /></svg>
        </span>
        <h3 class="text-lg font-bold text-ink">Задание дня</h3>
    </div>

    @if ($exercise)
        <p class="text-sm text-ink/60">{{ $exercise->lesson->title ?? 'Практика' }}</p>
        <p class="mt-1 text-lg font-bold text-ink">{{ $exercise->title }}</p>
        <x-ui.badge variant="accent" class="mt-3">{{ $exercise->type_label }}</x-ui.badge>

        <x-ui.button href="{{ route('exercises.show', $exercise->id) }}" variant="primary" class="mt-5 w-full">
            Выполнить задание
        </x-ui.button>
    @else
        <p class="text-sm text-ink/60">Вы уже сделали все доступные упражнения на сегодня. Отличная работа!</p>
    @endif
</x-ui.card>
