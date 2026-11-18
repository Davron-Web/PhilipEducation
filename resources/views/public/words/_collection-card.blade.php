{{-- Карточка одной коллекции на хабе словаря (words/index.blade.php).
     Ожидает $collection (withCount('words')) и $learned (int, сколько слов
     из неё уже выучено текущим пользователем). --}}
@php
    $total = $collection->words_count;
    $percent = $total > 0 ? min(100, round($learned / $total * 100)) : 0;

    $countLabel = match (app()->getLocale()) {
        'en' => $total.' '.($total === 1 ? 'word' : 'words'),
        'tg' => $total.' калима',
        default => $total.' '.(function (int $n): string {
            $mod100 = $n % 100;
            $mod10 = $n % 10;
            if ($mod100 >= 11 && $mod100 <= 14) return 'слов';
            if ($mod10 === 1) return 'слово';
            if ($mod10 >= 2 && $mod10 <= 4) return 'слова';

            return 'слов';
        })($total),
    };
@endphp

<a
    href="{{ route('words.collections.show', $collection->slug) }}"
    class="card-lift group flex flex-col rounded-2xl border border-line bg-armor2/70 p-5 shadow-soft transition duration-300 ease-out hover:-translate-y-1 hover:border-brand/30"
    data-reveal
>
    <h3 class="text-lg font-bold text-ink">{{ $collection->title }}</h3>

    @if ($total > 0)
        <p class="mt-1 text-sm text-ink/50">{{ __('site.vocabulary_collections.learned_of_total', ['learned' => $learned, 'total' => $total]) }}</p>
        <div class="mt-3 h-1.5 w-full overflow-hidden rounded-full bg-surface2">
            <div class="h-full rounded-full bg-brand" style="width: {{ $percent }}%"></div>
        </div>
    @else
        <p class="mt-1 text-sm text-ink/40">{{ __('site.vocabulary_collections.coming_soon') }}</p>
    @endif

    <span class="mt-4 inline-flex items-center gap-1.5 text-xs font-bold uppercase tracking-wider text-brand">
        {{ $countLabel }}
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="transition group-hover:translate-x-1"><path d="M5 12h14M13 5l7 7-7 7" /></svg>
    </span>
</a>
