@extends('layouts.app')

@section('title', 'Exercises')
@section('page_title', 'Exercises')
@section('page_description', $exercises->count() . ' exercises available')

@section('content')
    @if($exercises->isEmpty())
        <div class="text-center py-5">
            <i class="bi bi-pencil-square display-4 text-secondary"></i>
            <p class="text-secondary mt-3 mb-0">No exercises published yet. Check back soon!</p>
        </div>
    @else
        @php
            $byLevel = $exercises->groupBy(fn ($exercise) => optional(optional($exercise->lesson)->level)->id ?? 0);
            $moduleIndex = 0;
        @endphp

        <div class="ph-modules">
            @foreach($byLevel as $levelId => $levelExercises)
                @php
                    $level = optional($levelExercises->first()->lesson)->level;
                    $moduleIndex++;
                @endphp
                <div class="ph-module">
                    <div>
                        <div class="ph-module-eyebrow">Модуль {{ $moduleIndex }} {{ $level ? '· ' . $level->code : '' }}</div>
                        <h2 class="ph-module-title">{{ $level->name ?? 'Общий уровень' }}</h2>
                        <p class="ph-module-desc">{{ $level->description ?? 'Упражнения без привязки к уровню.' }}</p>
                    </div>
                    <div class="ph-module-steps">
                        @foreach($levelExercises as $exercise)
                            <a href="{{ route('exercises.show', $exercise->id) }}" class="ph-step">
                                <span class="ph-step-dot"></span>
                                <span>{{ $exercise->title }}</span>
                                <span class="ph-step-progress">{{ $exercise->questions_count }} вопр.</span>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    @endif
@endsection
