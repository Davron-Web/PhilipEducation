{{-- Карточка одного слова в списке — общий партиал для старого плоского
     грида (words/index.blade.php, ветка ?category=) и страницы коллекции
     (words/collection.blade.php). Ожидает $word, $isLearned в области
     видимости; $showCategory (bool, по умолчанию false) — показывать ли
     старый тег category (нужно только во "Все слова"). --}}
@php $showCategory = $showCategory ?? false; @endphp

<a
    href="{{ route('words.show', $word->id) }}"
    class="group flex h-full flex-col rounded-2xl border border-line bg-armor2/70 p-5 shadow-lg shadow-ink/5 backdrop-blur-xl transition duration-300 ease-out hover:-translate-y-1.5 hover:border-brand/30 hover:shadow-2xl hover:shadow-brand/15"
    data-reveal
>
    <div class="mb-2 flex items-start justify-between gap-2">
        <div>
            <h3 class="flex items-center gap-1.5 text-lg font-bold capitalize text-ink">
                {{ $word->word }}
                <x-speak-button :word="$word->word" :audio-url="$word->audio_url" />
            </h3>
            @if ($word->transcription)
                <span class="text-sm text-ink/40">/{{ $word->transcription }}/</span>
            @endif
        </div>
        <x-ui.badge :variant="$isLearned ? 'success' : 'neutral'">{{ $isLearned ? 'Выучено' : 'Новое' }}</x-ui.badge>
    </div>

    @if ($word->translations->isNotEmpty())
        <p class="font-semibold text-brand">{{ $word->translations->pluck('translation')->join(', ') }}</p>
    @endif

    @if ($word->example)
        <p class="mt-2 flex-1 text-sm italic text-ink/50">&laquo;{{ $word->example }}&raquo;</p>
    @endif

    @if ($showCategory && $word->category)
        <span class="mt-3 inline-flex w-fit items-center rounded-full bg-sun/10 border border-sun/30 px-2.5 py-0.5 text-xs font-bold text-sun">{{ $word->category }}</span>
    @endif
</a>
