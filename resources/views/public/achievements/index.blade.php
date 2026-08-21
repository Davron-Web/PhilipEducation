@extends('layouts.app')

@section('title', 'Achievements')
@section('page_title', 'Achievements')
@section('page_description', $earned->count() . ' of ' . $achievements->count() . ' earned')

@section('content')
    @if($achievements->isEmpty())
        <div class="text-center py-5">
            <i class="bi bi-trophy display-4 text-secondary"></i>
            <p class="text-secondary mt-3 mb-0">No achievements have been set up yet. Check back soon!</p>
        </div>
    @else
        <div class="row g-3">
            @foreach($achievements as $achievement)
                <div class="col-12 col-sm-6 col-lg-4 col-xl-3">
                    <x-achievement-card :achievement="$achievement" :earned="$earned->get($achievement->id)" />
                </div>
            @endforeach
        </div>
    @endif
@endsection
