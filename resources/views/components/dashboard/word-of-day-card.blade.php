{{-- «Слово дня» — реальное слово из словаря (Word + WordTranslation),
     выбор детерминированный по дню года (DashboardController::wordOfTheDay()),
     не случайное на каждый заход. Пустое состояние — если словарь вообще
     пуст (в проде такого не бывает, но на чистой БД возможно). --}}
@props([
    'word' => null,
    'delay' => 0,
])

<x-ui.card class="!p-6" :hover="true" style="transition-delay: {{ $delay }}ms">
    <div class="mb-4 flex items-center gap-2">
        <span class="acc-icon flex h-9 w-9 items-center justify-center rounded-xl" style="--accent: var(--pe-gold)">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20M4 19.5A2.5 2.5 0 0 0 6.5 22H20V4H6.5A2.5 2.5 0 0 0 4 6.5v13Z" /></svg>
        </span>
        <h3 class="text-lg font-bold text-ink">Слово дня</h3>
    </div>

    @if ($word)
        <div class="flex items-center gap-2">
            <span class="text-2xl font-extrabold capitalize text-ink">{{ $word->word }}</span>
            <x-speak-button :word="$word->word" :audio-url="$word->audio_url" />
        </div>
        @if ($word->transcription)
            <p class="mt-0.5 text-sm text-ink/40">/{{ $word->transcription }}/</p>
        @endif

        @if ($word->translations->isNotEmpty())
            <p class="mt-3 text-lg font-semibold text-brand">{{ $word->translations->pluck('translation')->join(', ') }}</p>
        @endif

        @if ($word->example)
            <p class="mt-2 text-sm italic text-ink/60">&laquo;{{ $word->example }}&raquo;</p>
        @endif

        <x-ui.button href="{{ route('words.show', $word->id) }}" variant="outline" class="mt-5 w-full">Открыть в словаре</x-ui.button>
    @else
        <p class="text-sm text-ink/60">Словарь пока пуст — загляните позже.</p>
    @endif
</x-ui.card>
