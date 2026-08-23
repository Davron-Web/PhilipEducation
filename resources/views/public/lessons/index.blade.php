@extends('layouts.app')

@section('title', 'Lessons')
@section('page_title', 'Lessons')
@section('page_description', $lessons->count() . ' lessons available')

@section('content')
    @if($lessons->isEmpty())
        <div class="text-center py-5">
            <i class="bi bi-journal-x display-4 text-secondary"></i>
            <p class="text-secondary mt-3 mb-0">No lessons published yet. Check back soon!</p>
        </div>
    @else
        @php
            $byLevel = $lessons->groupBy(fn ($lesson) => optional($lesson->level)->id ?? 0);
            $moduleIndex = 0;
        @endphp

        <div class="ph-modules">
            @foreach($byLevel as $levelId => $levelLessons)
                @php
                    $level = $levelLessons->first()->level;
                    $moduleIndex++;
                @endphp
                <div class="ph-module">
                    <div>
                        <div class="ph-module-eyebrow">Модуль {{ $moduleIndex }} {{ $level ? '· ' . $level->code : '' }}</div>
                        <h2 class="ph-module-title">{{ $level->name ?? 'Общий уровень' }}</h2>
                        <p class="ph-module-desc">{{ $level->description ?? 'Уроки без привязки к уровню.' }}</p>
                    </div>
                    <div class="ph-module-steps">
                        @foreach($levelLessons as $lesson)
                            @php $percent = (int) ($progressByLesson[$lesson->id] ?? 0); @endphp
                            <a href="{{ route('lessons.show', $lesson->id) }}"
                               class="ph-step {{ $percent >= 100 ? 'is-done' : ($percent > 0 ? 'is-current' : '') }}">
                                <span class="ph-step-dot">
                                    @if($percent >= 100)
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                                    @endif
                                </span>
                                <span>{{ $lesson->title }}</span>
                                @if($percent > 0)
                                    <span class="ph-step-progress">{{ $percent }}%</span>
                                @endif
                            </a>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    @endif
@endsection
