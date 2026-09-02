@props(['test'])

<div class="flex h-full flex-col rounded-2xl border border-line bg-armor2 p-5 shadow-soft card-lift">
    <div class="mb-2 flex items-center justify-between">
        <x-ui.badge variant="level" :level="optional($test->lesson)->level" />
        <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-brand/10 text-brand">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 11l3 3L22 4" /><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11" /></svg>
        </span>
    </div>

    <h3 class="mb-2 text-base font-bold text-ink">{{ $test->title }}</h3>

    <div class="mb-3 flex items-center gap-3 text-sm text-ink/50">
        <span class="inline-flex items-center gap-1">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10" /><path d="M12 16v-4M12 8h.01" /></svg>
            {{ $test->questions_count ?? $test->questions->count() }} вопросов
        </span>
        @if ($test->time_limit)
            <span class="inline-flex items-center gap-1">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10" /><polyline points="12 6 12 12 16 14" /></svg>
                {{ $test->time_limit }} мин
            </span>
        @endif
    </div>

    <x-ui.button :href="route('tests.show', $test->id)" size="sm" class="mt-auto">
        Начать тест
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6" /></svg>
    </x-ui.button>
</div>
