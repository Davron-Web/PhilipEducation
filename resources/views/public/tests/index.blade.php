@extends('layouts.app')

@section('title', 'Tests')
@section('page_title', 'Tests')
@section('page_description', 'Check your knowledge by level')

@section('content')
    @if($testsByLevel->isEmpty())
        <div class="text-center py-5">
            <i class="bi bi-clipboard-check display-4 text-secondary"></i>
            <p class="text-secondary mt-3 mb-0">No tests published yet. Check back soon!</p>
        </div>
    @else
        @foreach($testsByLevel as $level => $tests)
            <div class="d-flex align-items-center gap-2 mb-3">
                <h2 class="h6 fw-bold mb-0">{{ $level }}</h2>
                <span class="text-secondary small">{{ $tests->count() }} {{ Str::plural('test', $tests->count()) }}</span>
            </div>
            <div class="row g-3 mb-4">
                @foreach($tests as $test)
                    <div class="col-12 col-md-6 col-lg-4 col-xl-3">
                        <x-test-card :test="$test" />
                    </div>
                @endforeach
            </div>
        @endforeach
    @endif
@endsection
