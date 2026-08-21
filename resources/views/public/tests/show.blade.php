@extends('layouts.app')

@section('title', $test->title)
@section('page_title', 'Test')
@section('page_description', optional(optional($test->lesson)->level)->name ?? 'Self-check quiz')

@section('content')
    <div class="mb-3">
        <a href="{{ route('tests.index') }}" class="link-secondary text-decoration-none small">
            <i class="bi bi-arrow-left me-1"></i>Back to tests
        </a>
    </div>

    <div class="card shadow-sm rounded-4 border-0 mb-4">
        <div class="card-body p-4">
            <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                <x-level-badge :level="optional($test->lesson)->level" />
                <span class="text-secondary small"><i class="bi bi-question-circle me-1"></i>{{ $test->questions->count() }} questions</span>
                @if($test->time_limit)
                    <span class="text-secondary small"><i class="bi bi-clock me-1"></i>{{ $test->time_limit }} min</span>
                @endif
                <span class="text-secondary small"><i class="bi bi-check2-square me-1"></i>Passing score: {{ $test->passing_score }}%</span>
            </div>
            <h1 class="h4 fw-bold mb-0">{{ $test->title }}</h1>
        </div>
    </div>

    @if($test->questions->isEmpty())
        <div class="text-center py-5">
            <i class="bi bi-question-diamond display-4 text-secondary"></i>
            <p class="text-secondary mt-3 mb-0">No questions added to this test yet.</p>
        </div>
    @else
        <form data-test-quiz class="card shadow-sm rounded-4 border-0">
            <div class="card-body p-4">
                <div class="alert d-none" data-quiz-result role="alert"></div>

                @foreach($test->questions as $question)
                    <div class="mb-4" data-question>
                        <p class="fw-medium mb-2">{{ $loop->iteration }}. {{ $question->question }}</p>

                        @if($question->answers->isNotEmpty())
                            @foreach($question->answers as $answer)
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="question-{{ $question->id }}"
                                           id="answer-{{ $answer->id }}" data-correct="{{ $answer->is_correct ? 1 : 0 }}">
                                    <label class="form-check-label" for="answer-{{ $answer->id }}">{{ $answer->answer }}</label>
                                </div>
                            @endforeach
                        @else
                            <input type="text" class="form-control" placeholder="Your answer…" disabled>
                            <div class="form-text">Open-ended question — not self-gradable.</div>
                        @endif
                    </div>
                @endforeach

                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-flag me-1"></i>Submit Test
                </button>
            </div>
        </form>
    @endif
@endsection
