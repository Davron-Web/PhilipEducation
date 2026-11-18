{{-- Карточка определения уровня — отдельная от бейджа в шапке: там просто
     показан текущий уровень (идентичность), здесь — действие (пройти/
     перепройти placement-тест). Роут tests.placement.intro — тест живёт
     внутри раздела «Тесты» (перенесён туда отдельной задачей ранее). --}}
@props(['user' => null, 'delay' => 0])

@php $user = $user ?? auth()->user(); @endphp

<x-ui.card class="!p-6" :hover="true" style="transition-delay: {{ $delay }}ms">
    <div class="mb-3 flex items-center gap-2">
        <span class="acc-icon flex h-9 w-9 items-center justify-center rounded-xl" style="--accent: var(--pe-sky)">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 11l3 3L22 4" /><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11" /></svg>
        </span>
        <h3 class="text-lg font-bold text-ink">Уровень английского</h3>
    </div>

    @if ($user->level_id)
        <p class="text-sm text-ink/60">
            Ваш уровень: <span class="font-bold text-ink">{{ $user->level->code }}</span> — {{ $user->level->name }}
        </p>
        <x-ui.button href="{{ route('tests.placement.intro') }}" variant="outline" class="mt-4 w-full">Пройти заново</x-ui.button>
    @else
        <p class="text-sm text-ink/60">
            Ещё не определён — пройдите тест из 50 вопросов, чтобы уроки и рекомендации подобрались под вас.
        </p>
        <x-ui.button href="{{ route('tests.placement.intro') }}" class="mt-4 w-full">Определить уровень</x-ui.button>
    @endif
</x-ui.card>
