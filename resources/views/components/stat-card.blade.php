@props([
    'icon' => 'bi-graph-up',
    'number' => 0,
    'title' => '',
    'description' => '',
    'percent' => null,
    'color' => 'primary',
])

<div class="card card-hover shadow-sm h-100 rounded-4 border-0">
    <div class="card-body p-4">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <span class="d-inline-flex align-items-center justify-content-center rounded-3 bg-{{ $color }}-subtle text-{{ $color === 'accent' ? 'accent' : $color }}"
                  style="width: 3rem; height: 3rem;">
                <i class="bi {{ $icon }} fs-4"></i>
            </span>
        </div>
        <h3 class="fw-bold mb-0">{{ $number }}</h3>
        <p class="text-secondary small mb-0">{{ $title }}</p>
        @if($description)
            <p class="text-secondary mb-0" style="font-size: .75rem;">{{ $description }}</p>
        @endif

        @if(!is_null($percent))
            <div class="progress mt-3" style="height: 6px;">
                <div class="progress-bar bg-{{ $color === 'accent' ? 'accent' : $color }}" role="progressbar"
                     style="width: {{ $percent }}%" aria-valuenow="{{ $percent }}" aria-valuemin="0" aria-valuemax="100"></div>
            </div>
        @endif
    </div>
</div>
