@props([
    'icon' => 'bi-graph-up',
    'number' => 0,
    'title' => '',
    'description' => '',
    'percent' => null,
    'color' => 'brand',
])

@php
    $icons = [
        'bi-journal-check' => '<path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/>',
        'bi-translate' => '<path d="M5 8h9M9 4v4M12 8c-1 3.5-3.5 6-7 8M6 12c1.5 1.5 4 2.5 6 3"/><path d="M14 20l4-9 4 9M15.5 17h5"/>',
        'bi-clipboard-check' => '<rect x="6" y="4" width="12" height="17" rx="2"/><path d="M9 4V2h6v2M9 12l2 2 4-4"/>',
        'bi-trophy' => '<path d="M8 21h8M12 17v4M7 4h10v5a5 5 0 0 1-10 0V4z"/><path d="M17 5h3a2 2 0 0 1-2 4M7 5H4a2 2 0 0 0 2 4"/>',
    ];
    $iconPath = $icons[$icon] ?? '<circle cx="12" cy="12" r="9"/>';

    $badgePalette = [
        'brand' => 'bg-brand/10 text-brand',
        'primary' => 'bg-brand/10 text-brand',
        'success' => 'bg-green-500/10 text-green-600 dark:text-green-400',
        'info' => 'bg-sky/10 text-sky',
        'accent' => 'bg-sun/10 text-sun',
    ];
    $badgeClasses = $badgePalette[$color] ?? $badgePalette['brand'];

    $barPalette = [
        'brand' => 'bg-brand',
        'primary' => 'bg-brand',
        'success' => 'bg-green-500',
        'info' => 'bg-sky',
        'accent' => 'bg-sun',
    ];
    $barClass = $barPalette[$color] ?? $barPalette['brand'];
@endphp

<div class="rounded-2xl border border-line bg-armor2 p-6 shadow-soft card-lift">
    <span class="mb-3 inline-flex h-12 w-12 items-center justify-center rounded-xl {{ $badgeClasses }}">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">{!! $iconPath !!}</svg>
    </span>
    <h3 class="text-2xl font-extrabold text-ink">{{ $number }}</h3>
    <p class="text-sm text-ink/50">{{ $title }}</p>
    @if ($description)
        <p class="mt-0.5 text-xs text-ink/40">{{ $description }}</p>
    @endif

    @if (! is_null($percent))
        <div class="mt-3 h-1.5 overflow-hidden rounded-full bg-surface2">
            <div class="h-full rounded-full {{ $barClass }}" style="width: {{ $percent }}%"></div>
        </div>
    @endif
</div>
