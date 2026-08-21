@props(['achievement', 'earned' => null])

@php
    $isEarned = (bool) $earned;
    $isImageIcon = $achievement->icon && (str_starts_with($achievement->icon, 'http://') || str_starts_with($achievement->icon, 'https://'));
@endphp

<div class="card shadow-sm h-100 rounded-4 border-0 {{ $isEarned ? 'card-hover' : 'achievement-locked' }}">
    <div class="card-body p-4 text-center d-flex flex-column align-items-center">
        <span class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3 {{ $isEarned ? 'bg-accent-subtle' : 'bg-secondary-subtle' }}"
              style="width: 4rem; height: 4rem;">
            @if($isImageIcon)
                <img src="{{ $achievement->icon }}" alt="" class="rounded-circle" style="width: 3rem; height: 3rem; object-fit: cover;">
            @else
                <i class="bi {{ $isEarned ? 'bi-trophy-fill text-accent' : 'bi-lock-fill text-secondary' }} fs-3"></i>
            @endif
        </span>

        <h3 class="h6 fw-bold mb-1">{{ $achievement->title }}</h3>
        <p class="text-secondary small mb-3">{{ $achievement->description }}</p>

        <div class="mt-auto d-flex flex-column align-items-center gap-1">
            <span class="badge {{ $isEarned ? 'text-bg-accent' : 'text-bg-secondary' }} rounded-pill">
                <i class="bi bi-star-fill me-1"></i>{{ $achievement->points }} pts
            </span>
            @if($isEarned && $earned->pivot->earned_at)
                <span class="text-secondary" style="font-size: .75rem;">
                    Earned {{ \Illuminate\Support\Carbon::parse($earned->pivot->earned_at)->format('M j, Y') }}
                </span>
            @endif
        </div>
    </div>
</div>
