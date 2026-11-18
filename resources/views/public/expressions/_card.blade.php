{{-- Карточка одного выражения в списке. Вынесена из index.blade.php, чтобы
     не дублировать разметку между плоским списком и списком по подгруппам
     (см. index.blade.php: секция "Список" и секция "Подгруппы"). Ожидает
     $expression, $isLearned, $selectedCategory, $types в области видимости. --}}
<a
    href="{{ route('expressions.show', $expression->id) }}"
    class="group flex h-full flex-col rounded-2xl border border-line bg-armor2/70 p-5 shadow-lg shadow-ink/5 backdrop-blur-xl transition duration-300 ease-out hover:-translate-y-1.5 hover:border-brand/30 hover:shadow-2xl hover:shadow-brand/15"
    data-reveal
>
    <div class="mb-2 flex items-start justify-between gap-2">
        <div>
            <h3 class="flex items-center gap-1.5 text-lg font-bold text-ink">
                {{ $expression->text }}
                <x-speak-button :word="$expression->text" :audio-url="$expression->audio_url" />
            </h3>
            @if ($expression->transcription)
                <span class="text-sm text-ink/40">/{{ $expression->transcription }}/</span>
            @endif
        </div>
        <x-ui.badge :variant="$isLearned ? 'success' : 'neutral'">{{ $isLearned ? 'Выучено' : 'Новое' }}</x-ui.badge>
    </div>

    <div class="mb-1 flex flex-wrap items-center gap-2">
        <x-ui.badge variant="accent">{{ $types[$expression->type] ?? $expression->type }}</x-ui.badge>
        @if ($expression->register)
            <x-ui.badge variant="register" :register="$expression->register" />
        @endif
        @if ($expression->level)
            <x-ui.badge variant="level" :level="$expression->level" />
        @endif
    </div>

    @if ($expression->translations->isNotEmpty())
        <div class="flex flex-wrap items-baseline gap-x-4 gap-y-0.5">
            @foreach ($expression->translations as $translation)
                <p class="inline-flex items-baseline gap-1 font-semibold text-brand">
                    {{ $translation->translation }}
                    <span class="text-[10px] font-bold uppercase text-brand/50">{{ $translation->language }}</span>
                </p>
            @endforeach
        </div>
    @endif

    @if ($expression->example)
        <p class="mt-2 flex-1 text-sm italic text-ink/50">&laquo;{{ $expression->example }}&raquo;</p>
    @endif

    @if ($selectedCategory === 'all' && $expression->category)
        <span class="mt-3 inline-flex w-fit items-center rounded-full bg-sun/10 border border-sun/30 px-2.5 py-0.5 text-xs font-bold text-sun">{{ $expression->category }}</span>
    @endif
</a>
