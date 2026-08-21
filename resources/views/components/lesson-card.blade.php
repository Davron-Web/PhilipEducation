@props(['lesson', 'progress' => 0])

<div class="card card-hover shadow-sm h-100 rounded-4 border-0">
    <div class="card-body p-4 d-flex flex-column">
        <div class="d-flex align-items-center justify-content-between mb-2">
            <x-level-badge :level="$lesson->level" />
            @if($progress >= 100)
                <span class="badge text-bg-success rounded-pill"><i class="bi bi-check-circle-fill me-1"></i>Done</span>
            @endif
        </div>

        <h3 class="h6 fw-bold mb-1">{{ $lesson->title }}</h3>
        <p class="text-secondary small mb-3 flex-grow-1">{{ Str::limit($lesson->description, 90) }}</p>

        <div class="d-flex align-items-center gap-3 text-secondary small mb-3">
            <span><i class="bi bi-clock me-1"></i>{{ $lesson->estimated_minutes }} min</span>
        </div>

        <div class="progress mb-3" style="height: 6px;">
            <div class="progress-bar" role="progressbar" style="width: {{ $progress }}%"></div>
        </div>

        <a href="{{ route('lessons.show', $lesson->id) }}" class="btn btn-primary btn-sm mt-auto">
            {{ $progress >= 100 ? 'Review Lesson' : ($progress > 0 ? 'Continue' : 'Start Lesson') }}
            <i class="bi bi-arrow-right ms-1"></i>
        </a>
    </div>
</div>
