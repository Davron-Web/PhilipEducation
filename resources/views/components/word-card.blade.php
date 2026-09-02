@props(['word', 'learned' => false])

<div class="group flex h-full flex-col overflow-hidden rounded-2xl border border-line bg-armor2 shadow-soft card-lift">
    @if ($word->image)
        <img src="{{ asset($word->image) }}" alt="{{ $word->word }}" class="h-36 w-full object-cover">
    @endif
    <div class="flex flex-1 flex-col p-5">
        <div class="mb-2 flex items-start justify-between gap-2">
            <div>
                <h3 class="flex items-center gap-1.5 text-base font-bold capitalize text-ink">
                    {{ $word->word }}
                    <x-speak-button :word="$word->word" :audio-url="$word->audio_url" />
                </h3>
                @if ($word->transcription)
                    <span class="text-sm text-ink/40">/{{ $word->transcription }}/</span>
                @endif
            </div>
            <x-ui.badge :variant="$learned ? 'success' : 'neutral'">{{ $learned ? 'Выучено' : 'Новое' }}</x-ui.badge>
        </div>

        @if ($word->translations->isNotEmpty())
            <p class="mb-2 font-semibold text-brand">{{ $word->translations->pluck('translation')->join(', ') }}</p>
        @endif

        @if ($word->example)
            <p class="mb-3 flex-1 text-sm italic text-ink/50">&laquo;{{ $word->example }}&raquo;</p>
        @endif

        <a href="{{ route('words.show', $word->id) }}" class="mt-auto inline-flex items-center gap-1 text-sm font-bold text-brand hover:underline">
            Смотреть слово
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6" /></svg>
        </a>
    </div>
</div>
