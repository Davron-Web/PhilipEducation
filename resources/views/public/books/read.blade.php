@extends('layouts.app')

@section('title', $book->title)
@section('page_title', 'Books')
@section('page_description', $book->title . ' — page ' . $page . ' of ' . $totalPages)

@section('content')
    <div class="mb-3">
        <a href="{{ route('books.index') }}" class="link-secondary text-decoration-none small">
            <i class="bi bi-arrow-left me-1"></i>Back to books
        </a>
    </div>

    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
        <div>
            <h1 class="h5 fw-bold mb-1">{{ $book->title }}</h1>
            <span class="text-secondary small">by {{ $book->author }}</span>
            <x-level-badge :level="$book->level" class="ms-2" />
        </div>
        <div class="d-flex align-items-center gap-2">
            <button type="button" id="fontDecrease" class="btn btn-outline-secondary btn-sm" title="Smaller text">A-</button>
            <button type="button" id="fontIncrease" class="btn btn-outline-secondary btn-sm" title="Larger text">A+</button>
        </div>
    </div>

    <div class="progress mb-4" style="height:6px;">
        <div class="progress-bar bg-primary" role="progressbar" style="width: {{ round($page / max(1, $totalPages) * 100) }}%"></div>
    </div>

    {{-- Voice control panel --}}
    <div class="card shadow-sm rounded-4 border-0 mb-3">
        <div class="card-body p-3 d-flex flex-wrap align-items-center gap-3">
            <div class="btn-group" role="group" aria-label="Playback controls">
                <button type="button" id="playBtn" class="btn btn-primary btn-sm"><i class="bi bi-play-fill"></i> Read</button>
                <button type="button" id="pauseBtn" class="btn btn-outline-primary btn-sm" disabled><i class="bi bi-pause-fill"></i> Pause</button>
                <button type="button" id="stopBtn" class="btn btn-outline-secondary btn-sm" disabled><i class="bi bi-stop-fill"></i> Stop</button>
            </div>

            <div class="d-flex align-items-center gap-2">
                <span class="text-secondary small">Speed:</span>
                <div class="btn-group" role="group" aria-label="Reading speed">
                    <button type="button" class="btn btn-outline-secondary btn-sm speed-btn" data-rate="0.75">0.75x</button>
                    <button type="button" class="btn btn-outline-secondary btn-sm speed-btn active" data-rate="0.9">0.9x</button>
                    <button type="button" class="btn btn-outline-secondary btn-sm speed-btn" data-rate="1.0">1.0x</button>
                </div>
            </div>

            <div class="d-flex align-items-center gap-2">
                <span class="text-secondary small">Mode:</span>
                <div class="btn-group" role="group" aria-label="Reading mode">
                    <button type="button" class="btn btn-outline-secondary btn-sm mode-btn active" data-mode="continuous">Continuous</button>
                    <button type="button" class="btn btn-outline-secondary btn-sm mode-btn" data-mode="sentence">By sentence</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Practice UI, shown only in sentence mode after each sentence --}}
    <div id="practiceBar" class="alert alert-primary d-none align-items-center justify-content-between flex-wrap gap-2 rounded-4" role="status">
        <span class="fw-semibold">Your turn 👇 — repeat the sentence out loud</span>
        <div class="d-flex gap-2">
            <button type="button" id="repeatBtn" class="btn btn-sm btn-outline-primary">🔁 Repeat</button>
            <button type="button" id="nextSentenceBtn" class="btn btn-sm btn-primary">▶ Next</button>
        </div>
    </div>

    <div class="card shadow-sm rounded-4 border-0 mb-4">
        <div class="card-body p-4">
            @if($currentPage->title)
                <h2 class="h6 fw-bold mb-3">{{ $currentPage->title }}</h2>
            @endif

            <div id="readerContent" class="reader-content">
                @foreach(preg_split('/\n\s*\n/', trim($currentPage->content ?? '')) as $paragraph)
                    @php $paragraph = trim($paragraph); @endphp
                    @if($paragraph !== '')
                        <p class="reader-paragraph">{{ $paragraph }}</p>
                    @endif
                @endforeach
            </div>
        </div>
    </div>

    <div class="d-flex align-items-center justify-content-between">
        @if($page > 1)
            <a href="{{ route('books.read', $book) }}?page={{ $page - 1 }}" class="btn btn-outline-primary">
                <i class="bi bi-arrow-left me-1"></i>Back
            </a>
        @else
            <span></span>
        @endif

        <span class="text-secondary small">Page {{ $page }} of {{ $totalPages }}</span>

        @if($page < $totalPages)
            <a href="{{ route('books.read', $book) }}?page={{ $page + 1 }}" class="btn btn-primary">
                Forward<i class="bi bi-arrow-right ms-1"></i>
            </a>
        @else
            <span class="badge text-bg-success rounded-pill"><i class="bi bi-check-circle-fill me-1"></i>Last page</span>
        @endif
    </div>
@endsection

@push('scripts')
<script src="{{ asset('assets/js/pronounce.js') }}"></script>
<script src="{{ asset('assets/js/reader.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        initReader({
            saveUrl: {{ Js::from(route('books.progress', $book)) }},
            page: {{ (int) $page }},
        });
    });
</script>
@endpush
