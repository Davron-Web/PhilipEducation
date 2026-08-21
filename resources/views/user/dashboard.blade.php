@extends('layouts.app')

@section('title', 'Dashboard')
@section('page_title', 'Welcome back' . (auth()->user() ? ', ' . explode(' ', auth()->user()->name)[0] : '') . '!')
@section('page_description', 'Continue your learning journey')

@section('content')
    <div class="hero-banner p-4 p-lg-5 mb-4">
        <div class="row align-items-center position-relative">
            <div class="col-lg-8">
                <h2 class="display-6 fw-bold mb-2">Learn English. Build Your Future.</h2>
                <p class="fs-6 opacity-75 mb-4">Improve your English through interactive lessons, grammar, vocabulary and exercises.</p>
                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('lessons.index') }}" class="btn btn-accent fw-semibold">
                        <i class="bi bi-play-circle me-1"></i>Continue Learning
                    </a>
                    <a href="{{ route('lessons.index') }}" class="btn btn-outline-light">
                        Explore Lessons <i class="bi bi-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Stats --}}
    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-6 col-xl-3">
            <x-stat-card icon="bi-journal-check" :number="$stats['lessons_completed']" title="Lessons Completed"
                         description="Keep going!" :percent="$stats['lessons_progress']" color="primary" />
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <x-stat-card icon="bi-translate" :number="$stats['words_learned']" title="Vocabulary"
                         description="Words learned" :percent="$stats['words_progress']" color="success" />
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <x-stat-card icon="bi-clipboard-check" :number="$stats['tests_passed']" title="Tests Passed"
                         description="Great progress" :percent="$stats['tests_progress']" color="info" />
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <x-stat-card icon="bi-trophy" :number="$stats['achievements_count']" title="Achievements"
                         description="Badges earned" color="accent" />
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            {{-- Learning Progress --}}
            <div class="card shadow-sm rounded-4 border-0 mb-4">
                <div class="card-body p-4">
                    <h3 class="h6 fw-bold mb-3">Your Learning Progress</h3>
                    @php
                        $overall = round((($stats['lessons_progress'] ?? 0) + ($stats['words_progress'] ?? 0) + ($stats['tests_progress'] ?? 0)) / 3);
                    @endphp
                    <x-progress-bar label="Overall Progress" :percent="$overall" color="primary" />
                    <x-progress-bar label="Lessons" :percent="$stats['lessons_progress']" color="primary" />
                    <x-progress-bar label="Vocabulary" :percent="$stats['words_progress']" color="success" />
                    <x-progress-bar label="Tests" :percent="$stats['tests_progress']" color="info" />
                </div>
            </div>

            {{-- Continue Learning --}}
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h3 class="h6 fw-bold mb-0">Continue Learning</h3>
                <a href="{{ route('grammartopics.index') }}" class="small link-primary text-decoration-none">View all grammar</a>
            </div>
            <div class="row g-3">
                @php
                    $teasers = [
                        ['title' => 'Present Simple', 'level' => 'A1', 'description' => 'Habits, routines, and general truths.'],
                        ['title' => 'Past Simple', 'level' => 'A2', 'description' => 'Completed actions in the past.'],
                        ['title' => 'Present Continuous', 'level' => 'A1', 'description' => 'Actions happening right now.'],
                        ['title' => 'Present Perfect', 'level' => 'B1', 'description' => 'Connect past actions with the present.'],
                    ];
                @endphp
                @foreach($teasers as $teaser)
                    <div class="col-12 col-md-6">
                        <a href="{{ route('grammartopics.index') }}" class="card card-hover shadow-sm rounded-4 border-0 text-decoration-none text-body h-100">
                            <div class="card-body p-3 d-flex align-items-center gap-3">
                                <span class="d-inline-flex align-items-center justify-content-center rounded-3 bg-primary-subtle text-primary flex-shrink-0"
                                      style="width: 2.75rem; height: 2.75rem;">
                                    <i class="bi bi-diagram-3 fs-5"></i>
                                </span>
                                <div class="flex-grow-1">
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="fw-semibold small">{{ $teaser['title'] }}</span>
                                        <span class="badge text-bg-secondary rounded-pill">{{ $teaser['level'] }}</span>
                                    </div>
                                    <p class="text-secondary mb-0" style="font-size: .8rem;">{{ $teaser['description'] }}</p>
                                </div>
                                <i class="bi bi-chevron-right text-secondary"></i>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow-sm rounded-4 border-0 mb-4">
                <div class="card-body p-4">
                    <h3 class="h6 fw-bold mb-3">Practice</h3>
                    <p class="text-secondary small">Sharpen what you've learned with quick exercises.</p>
                    <a href="{{ route('exercises.index') }}" class="btn btn-outline-primary w-100">
                        <i class="bi bi-pencil-square me-1"></i>Practice Exercises
                    </a>
                </div>
            </div>

            <div class="card shadow-sm rounded-4 border-0">
                <div class="card-body p-4">
                    <h3 class="h6 fw-bold mb-3">Your Profile</h3>
                    <a href="{{ route('profiles.index') }}" class="d-flex align-items-center gap-3 text-decoration-none text-body">
                        <span class="avatar-circle" style="width: 3rem; height: 3rem;">
                            {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
                        </span>
                        <div>
                            <div class="fw-semibold">{{ auth()->user()->name }}</div>
                            <span class="small link-primary">View profile &rarr;</span>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
