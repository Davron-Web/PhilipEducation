@props(['word', 'audioUrl' => null])

<button
    type="button"
    title="Слушать"
    aria-label="Прослушать произношение: {{ $word }}"
    onclick="event.preventDefault(); event.stopPropagation(); pronounce({{ Js::from($word) }}, {{ Js::from($audioUrl ? asset($audioUrl) : null) }})"
    class="inline-flex h-7 w-7 items-center justify-center rounded-full bg-brand/10 text-brand transition hover:bg-brand hover:text-white active:scale-90"
>
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="14" height="14">
        <polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5" />
        <path d="M15.54 8.46a5 5 0 0 1 0 7.07" />
        <path d="M19.07 4.93a10 10 0 0 1 0 14.14" />
    </svg>
</button>
