@extends('layouts.app')

@section('title', $lesson->title)
@section('page_title', 'Lesson')
@section('page_description', optional($lesson->level)->name ?? 'Lesson details')

@section('content')
    <div class="mb-3">
        <a href="{{ route('lessons.index') }}" class="link-secondary text-decoration-none small">
            <i class="bi bi-arrow-left me-1"></i>Back to lessons
        </a>
    </div>

    <div class="card shadow-sm rounded-4 border-0 mb-4">
        <div class="card-body p-4">
            <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                <x-level-badge :level="$lesson->level" />
                <span class="text-secondary small"><i class="bi bi-clock me-1"></i>{{ $lesson->estimated_minutes }} min</span>
            </div>
            <h1 class="h4 fw-bold mb-2">{{ $lesson->title }}</h1>
            <p class="text-secondary mb-3">{{ $lesson->description }}</p>

            @if($progress && $progress->is_completed)
                <span class="badge text-bg-success rounded-pill"><i class="bi bi-check-circle-fill me-1"></i>Completed</span>
            @else
                <form method="POST" action="{{ route('public.lessons.complete', $lesson->id) }}">
                    @csrf
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle me-1"></i>Mark as Complete
                    </button>
                </form>
            @endif
        </div>
    </div>

    @if($lesson->contents->isNotEmpty())
        <h2 class="h6 fw-bold mb-3">Lesson Content</h2>
        <div class="accordion mb-4" id="lessonContentAccordion">
            @foreach($lesson->contents as $content)
                <div class="accordion-item">
                    <h3 class="accordion-header">
                        <button class="accordion-button {{ $loop->first ? '' : 'collapsed' }}" type="button"
                                data-bs-toggle="collapse" data-bs-target="#content-{{ $content->id }}">
                            {{ $content->title ?: 'Section ' . $loop->iteration }}
                        </button>
                    </h3>
                    <div id="content-{{ $content->id }}" class="accordion-collapse collapse {{ $loop->first ? 'show' : '' }}"
                         data-bs-parent="#lessonContentAccordion">
                        <div class="accordion-body">
                            <div class="mb-2 lesson-content-body">{!! $content->content !!}</div>
                            @if($content->file_url)
                                <a href="{{ $content->file_url }}" target="_blank" class="link-primary small">
                                    <i class="bi bi-paperclip me-1"></i>Attachment
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    @if($lesson->words->isNotEmpty())
        <h2 class="h6 fw-bold mb-3">Vocabulary in this lesson</h2>
        <div class="row g-3 mb-4">
            @foreach($lesson->words as $word)
                <div class="col-12 col-md-6 col-lg-4">
                    <x-word-card :word="$word" :learned="false" />
                </div>
            @endforeach
        </div>
    @endif

    @if($lesson->tests->isNotEmpty())
        <h2 class="h6 fw-bold mb-3">Related Tests</h2>
        <div class="row g-3">
            @foreach($lesson->tests as $test)
                <div class="col-12 col-md-6 col-lg-4">
                    <x-test-card :test="$test" />
                </div>
            @endforeach
        </div>
    @endif
@endsection
