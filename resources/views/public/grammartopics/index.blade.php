@extends('layouts.app')

@section('title', 'Grammar')
@section('page_title', 'Grammar Topics')
@section('page_description', $topics->count() . ' topics available')

@section('content')
    @php
        $levels = $topics->map(fn($t) => optional($t->level)->code)->filter()->unique()->sort()->values();
    @endphp

    @if($levels->isNotEmpty())
        <div class="btn-group flex-wrap mb-4" role="group" data-filter-buttons="[data-filter-item]">
            <button type="button" class="btn btn-outline-primary active" data-filter-value="all">All Levels</button>
            @foreach($levels as $level)
                <button type="button" class="btn btn-outline-primary" data-filter-value="{{ $level }}">{{ $level }}</button>
            @endforeach
        </div>
    @endif

    @if($topics->isEmpty())
        <div class="text-center py-5">
            <i class="bi bi-diagram-3 display-4 text-secondary"></i>
            <p class="text-secondary mt-3 mb-0">No grammar topics published yet. Check back soon!</p>
        </div>
    @else
        <div class="row g-3">
            @foreach($topics as $topic)
                <div class="col-12 col-md-6 col-lg-4 col-xl-3" data-filter-item="{{ optional($topic->level)->code ?? 'General' }}">
                    <x-grammar-card :topic="$topic" />
                </div>
            @endforeach
        </div>
    @endif
@endsection
