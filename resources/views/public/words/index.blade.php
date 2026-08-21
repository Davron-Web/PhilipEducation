@extends('layouts.app')

@section('title', 'Vocabulary')
@section('page_title', 'Vocabulary')
@section('page_description', $words->count() . ' words in your dictionary')

@section('content')
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-4">
        <div class="btn-group flex-wrap" role="group" data-filter-buttons="[data-filter-item]">
            <button type="button" class="btn btn-outline-primary active" data-filter-value="all">All Words</button>
            <button type="button" class="btn btn-outline-primary" data-filter-value="learned">Learned</button>
            <button type="button" class="btn btn-outline-primary" data-filter-value="new">New</button>
        </div>

        <a href="{{ route('exercises.index') }}" class="btn btn-accent fw-semibold">
            <i class="bi bi-pencil-square me-1"></i>Practice Vocabulary
        </a>
    </div>

    @if($words->isEmpty())
        <div class="text-center py-5">
            <i class="bi bi-translate display-4 text-secondary"></i>
            <p class="text-secondary mt-3 mb-0">No words published yet. Check back soon!</p>
        </div>
    @else
        <div class="row g-3">
            @foreach($words as $word)
                @php $isLearned = in_array($word->id, $learnedWordIds); @endphp
                <div class="col-12 col-md-6 col-lg-4 col-xl-3" data-filter-item="{{ $isLearned ? 'learned' : 'new' }}">
                    <x-word-card :word="$word" :learned="$isLearned" />
                </div>
            @endforeach
        </div>
    @endif
@endsection
