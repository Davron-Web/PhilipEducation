{{-- «Пора повторить» — только когда есть что повторять (SpacedRepetitionService).
     Компонент сам решает не рендериться при wordsDue=0 — проще, чем гонять
     @if вокруг тега компонента в основной вьюхе. --}}
@props(['wordsDue' => 0, 'delay' => 0])

@if ($wordsDue > 0)
    @php
        $m100 = $wordsDue % 100;
        $m10 = $wordsDue % 10;
        $wordForm = ($m100 >= 11 && $m100 <= 14) ? 'слов'
            : (($m10 === 1) ? 'слово' : (($m10 >= 2 && $m10 <= 4) ? 'слова' : 'слов'));
    @endphp
    <x-ui.card class="!p-6" :hover="true" style="transition-delay: {{ $delay }}ms">
        <div class="mb-3 flex items-center gap-2">
            <span class="acc-icon flex h-9 w-9 items-center justify-center rounded-xl" style="--accent: var(--pe-brand)">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 4v6h6M23 20v-6h-6" /><path d="M20.49 9A9 9 0 0 0 5.64 5.64L1 10m22 4-4.64 4.36A9 9 0 0 1 3.51 15" /></svg>
            </span>
            <h3 class="text-lg font-bold text-ink">Пора повторить</h3>
        </div>
        <p class="text-sm text-ink/60">
            {{ $wordsDue }} {{ $wordForm }} ждут повторения — это несколько минут,
            но именно они держат словарь в памяти.
        </p>
        <x-ui.button :href="route('words.review')" class="mt-4 w-full">Повторить</x-ui.button>
    </x-ui.card>
@endif
