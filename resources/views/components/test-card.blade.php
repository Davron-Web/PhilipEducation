@props(['test'])

<div class="card card-hover shadow-sm h-100 rounded-4 border-0">
    <div class="card-body p-4 d-flex flex-column">
        <div class="d-flex align-items-center justify-content-between mb-2">
            <x-level-badge :level="optional($test->lesson)->level" />
            <span class="d-inline-flex align-items-center justify-content-center rounded-3 bg-primary-subtle text-primary"
                  style="width: 2.25rem; height: 2.25rem;">
                <i class="bi bi-clipboard-check"></i>
            </span>
        </div>

        <h3 class="h6 fw-bold mb-2">{{ $test->title }}</h3>

        <div class="d-flex align-items-center gap-3 text-secondary small mb-3">
            <span><i class="bi bi-question-circle me-1"></i>{{ $test->questions_count ?? $test->questions->count() }} questions</span>
            @if($test->time_limit)
                <span><i class="bi bi-clock me-1"></i>{{ $test->time_limit }} min</span>
            @endif
        </div>

        <a href="{{ route('tests.show', $test->id) }}" class="btn btn-primary btn-sm mt-auto">
            Start Test <i class="bi bi-arrow-right ms-1"></i>
        </a>
    </div>
</div>
