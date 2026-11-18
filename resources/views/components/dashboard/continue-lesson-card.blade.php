{{-- «Продолжить обучение»: последний начатый, но не законченный урок, с
     настоящим прогресс-баром (UserProgress.progress_percent — то же поле,
     что использует каталог уроков, не выдумано для дашборда). Если такого
     урока нет — контроллер уже подставил первый непройденный урок уровня
     пользователя (см. DashboardController::index()), прогресс тогда 0.
     Пустое состояние — когда уроков вообще нет ни одного (пустая база
     или все уже пройдены). --}}
@props([
    'lesson' => null,
    'progress' => 0,
    'delay' => 0,
])

<div class="relative overflow-hidden rounded-2xl bg-brand p-6 text-white shadow-soft card-lift" data-reveal style="transition-delay: {{ $delay }}ms">
    <div class="relative">
        <p class="mb-2 inline-flex items-center gap-1.5 rounded-full bg-white/15 px-3 py-1 text-xs font-bold uppercase tracking-wide">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polygon points="5 3 19 12 5 21 5 3" /></svg>
            Продолжить обучение
        </p>

        @if ($lesson)
            <h2 class="text-2xl font-extrabold sm:text-3xl">{{ $lesson->title }}</h2>
            <p class="mt-2 max-w-md text-white/75">
                Урок уровня {{ optional($lesson->level)->code ?? '—' }}
                @if ($progress > 0)
                    · пройдено {{ $progress }}%
                @endif
            </p>

            @if ($progress > 0)
                <div class="mt-4 h-2 max-w-sm overflow-hidden rounded-full bg-white/20">
                    <div class="h-full rounded-full bg-white" data-fill-width="{{ $progress }}"></div>
                </div>
            @endif

            <x-ui.button href="{{ route('lessons.show', $lesson->id) }}" variant="accent" size="lg" class="mt-6">
                {{ $progress > 0 ? 'Продолжить урок' : 'Начать урок' }}
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M13 6l6 6-6 6" /></svg>
            </x-ui.button>
        @else
            <h2 class="text-2xl font-extrabold sm:text-3xl">Все доступные уроки пройдены!</h2>
            <p class="mt-2 max-w-md text-white/75">Загляните в словарь или тесты, чтобы закрепить знания.</p>
            <x-ui.button href="{{ route('words.index') }}" variant="accent" size="lg" class="mt-6">Повторить словарь</x-ui.button>
        @endif
    </div>
</div>
