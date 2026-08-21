@props(['topic'])

<div class="card card-hover shadow-sm h-100 rounded-4 border-0">
    <div class="card-body p-4 d-flex flex-column">
        <div class="d-flex align-items-center justify-content-between mb-2">
            <span class="d-inline-flex align-items-center justify-content-center rounded-3 bg-primary-subtle text-primary"
                  style="width: 2.5rem; height: 2.5rem;">
                <i class="bi bi-diagram-3 fs-5"></i>
            </span>
            <x-level-badge :level="$topic->level" />
        </div>

        <h3 class="h6 fw-bold mb-2">{{ $topic->title }}</h3>
        <p class="text-secondary small mb-3 flex-grow-1">{{ Str::limit(strip_tags($topic->theory), 100) }}</p>

        <a href="{{ route('grammartopics.show', $topic->id) }}" class="btn btn-outline-primary btn-sm mt-auto">
            Learn <i class="bi bi-arrow-right ms-1"></i>
        </a>
    </div>
</div>
