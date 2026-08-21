@props([
    'label' => '',
    'percent' => 0,
    'color' => 'primary',
])

<div class="mb-3">
    <div class="d-flex align-items-center justify-content-between mb-1">
        <span class="small fw-medium">{{ $label }}</span>
        <span class="small fw-bold text-{{ $color === 'accent' ? 'accent' : $color }}">{{ $percent }}%</span>
    </div>
    <div class="progress" style="height: 8px;">
        <div class="progress-bar bg-{{ $color === 'accent' ? 'accent' : $color }}" role="progressbar"
             style="width: {{ $percent }}%" aria-valuenow="{{ $percent }}" aria-valuemin="0" aria-valuemax="100"></div>
    </div>
</div>
