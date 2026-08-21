@extends('layouts.app')

@section('title', 'Exercises')
@section('page_title', 'Exercises')
@section('page_description', $exercises->count() . ' exercises available')

@section('content')
    @php
        $typeDescriptions = [
            'fill_blank' => 'Complete sentences by typing the missing word.',
            'matching' => 'Match related words, phrases or pairs.',
            'listening' => 'Listen and understand spoken English.',
            'speaking' => 'Practice pronunciation and speaking.',
            'translation' => 'Translate phrases between languages.',
        ];
    @endphp

    <h2 class="h6 fw-bold mb-3">Exercise Types</h2>
    <div class="row g-3 mb-4">
        @foreach(\App\Models\Exercise\Exercise::TYPES as $key => $label)
            <div class="col-12 col-sm-6 col-lg-4 col-xl-3">
                <div class="card shadow-sm rounded-4 border-0 h-100">
                    <div class="card-body p-3 text-center">
                        <span class="badge text-bg-{{ (new \App\Models\Exercise\Exercise(['type' => $key]))->type_badge_color }} rounded-pill mb-2">
                            {{ $label }}
                        </span>
                        <p class="text-secondary mb-0" style="font-size: .8rem;">{{ $typeDescriptions[$key] ?? '' }}</p>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <h2 class="h6 fw-bold mb-3">All Exercises</h2>

    @if($exercises->isEmpty())
        <div class="text-center py-5">
            <i class="bi bi-pencil-square display-4 text-secondary"></i>
            <p class="text-secondary mt-3 mb-0">No exercises published yet. Check back soon!</p>
        </div>
    @else
        <div class="row g-3">
            @foreach($exercises as $exercise)
                <div class="col-12 col-md-6 col-lg-4 col-xl-3">
                    <div class="card card-hover shadow-sm h-100 rounded-4 border-0">
                        <div class="card-body p-4 d-flex flex-column">
                            <span class="badge text-bg-{{ $exercise->type_badge_color }} rounded-pill mb-2 align-self-start">
                                {{ $exercise->type_label }}
                            </span>
                            <h3 class="h6 fw-bold mb-2">{{ $exercise->title }}</h3>
                            <p class="text-secondary small mb-3 flex-grow-1">{{ Str::limit($exercise->instructions, 90) }}</p>
                            <div class="d-flex align-items-center gap-2 text-secondary small mb-3">
                                <i class="bi bi-list-check"></i>{{ $exercise->questions_count }} questions
                            </div>
                            <a href="{{ route('exercises.show', $exercise->id) }}" class="btn btn-primary btn-sm mt-auto">
                                Practice <i class="bi bi-arrow-right ms-1"></i>
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
@endsection
