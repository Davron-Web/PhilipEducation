@extends('layouts.app')

@section('title', $exercise->title)
@section('page_title', 'Exercise')
@section('page_description', $exercise->type_label)

@section('content')
    <div class="mb-3">
        <a href="{{ route('exercises.index') }}" class="link-secondary text-decoration-none small">
            <i class="bi bi-arrow-left me-1"></i>Back to exercises
        </a>
    </div>

    <div class="card shadow-sm rounded-4 border-0 mb-4">
        <div class="card-body p-4">
            <span class="badge text-bg-{{ $exercise->type_badge_color }} rounded-pill mb-2">{{ $exercise->type_label }}</span>
            <h1 class="h4 fw-bold mb-2">{{ $exercise->title }}</h1>
            <p class="text-secondary mb-0">{{ $exercise->instructions }}</p>
        </div>
    </div>

    @if($exercise->questions->isEmpty())
        <div class="text-center py-5">
            <i class="bi bi-list-check display-4 text-secondary"></i>
            <p class="text-secondary mt-3 mb-0">No questions added to this exercise yet.</p>
        </div>
    @else
        <form data-exercise-check class="card shadow-sm rounded-4 border-0">
            <div class="card-body p-4">
                @foreach($exercise->questions as $question)
                    <div class="mb-4">
                        <label class="form-label fw-medium">{{ $loop->iteration }}. {{ $question->question }}</label>
                        <input type="text" class="form-control" placeholder="Type your answer…"
                               data-answer="{{ $question->correct_answer }}">
                    </div>
                @endforeach

                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check2-circle me-1"></i>Check Answers
                </button>
            </div>
        </form>
    @endif
@endsection
