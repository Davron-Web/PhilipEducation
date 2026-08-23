@extends('layouts.app')

@section('title', $topic->title)
@section('page_title', 'Grammar')
@section('page_description', optional($topic->level)->name ?? 'Grammar topic')

@section('content')
    <div class="mb-3">
        <a href="{{ route('grammartopics.index') }}" class="link-secondary text-decoration-none small">
            <i class="bi bi-arrow-left me-1"></i>Back to grammar topics
        </a>
    </div>

    <div class="card shadow-sm rounded-4 border-0">
        <div class="card-body p-4">
            <div class="d-flex align-items-center gap-2 mb-2">
                <x-level-badge :level="$topic->level" />
            </div>
            <h1 class="h4 fw-bold mb-3">{{ $topic->title }}</h1>
            <div class="text-body" style="white-space: pre-line;">{{ $topic->theory }}</div>
        </div>
    </div>

    @php $exercises = $topic->lessons->flatMap->exercises; @endphp
    @if($exercises->isNotEmpty())
        <div class="card shadow-sm rounded-4 border-0 mt-3">
            <div class="card-body p-4">
                <h2 class="h6 fw-bold mb-3"><i class="bi bi-pencil-square me-1"></i>Practice tasks</h2>
                <div class="list-group list-group-flush">
                    @foreach($exercises as $exercise)
                        <a href="{{ route('exercises.show', $exercise->id) }}"
                           class="list-group-item list-group-item-action d-flex align-items-center justify-content-between px-0">
                            <span>{{ $exercise->title }}</span>
                            <i class="bi bi-arrow-right text-secondary"></i>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    @endif
@endsection
