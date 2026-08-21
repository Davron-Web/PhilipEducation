@extends('layouts.app')

@section('title', 'Lessons')
@section('page_title', 'Lessons')
@section('page_description', $lessons->count() . ' lessons available')

@section('content')
    @php
        $levels = $lessons->map(fn($l) => optional($l->level)->code)->filter()->unique()->sort()->values();
    @endphp

    @if($levels->isNotEmpty())
        <div class="btn-group flex-wrap mb-4" role="group" data-filter-buttons="[data-filter-item]">
            <button type="button" class="btn btn-outline-primary active" data-filter-value="all">All Levels</button>
            @foreach($levels as $level)
                <button type="button" class="btn btn-outline-primary" data-filter-value="{{ $level }}">{{ $level }}</button>
            @endforeach
        </div>
    @endif

    @if($lessons->isEmpty())
        <div class="text-center py-5">
            <i class="bi bi-journal-x display-4 text-secondary"></i>
            <p class="text-secondary mt-3 mb-0">No lessons published yet. Check back soon!</p>
        </div>
    @else
        <div class="row g-3">
            @foreach($lessons as $lesson)
                <div class="col-12 col-md-6 col-lg-4 col-xl-3" data-filter-item="{{ optional($lesson->level)->code ?? 'General' }}">
                    <x-lesson-card :lesson="$lesson" :progress="$progressByLesson[$lesson->id] ?? 0" />
                </div>
            @endforeach
        </div>
    @endif
@endsection
