@extends('layouts.app')

@section('title', $user->name)
@section('page_title', 'Profile')
@section('page_description', 'Student profile')

@section('content')
    <div class="card shadow-sm rounded-4 border-0 mb-4">
        <div class="card-body p-4">
            <div class="d-flex flex-wrap align-items-center gap-3">
                <span class="avatar-circle" style="width: 4.5rem; height: 4.5rem; font-size: 1.5rem;">
                    {{ strtoupper(substr($user->name ?? 'U', 0, 1)) }}
                </span>
                <div>
                    <h1 class="h5 fw-bold mb-1">{{ $user->name }}</h1>
                    <x-level-badge :level="$user->level" />
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-12 col-sm-6 col-xl-3">
            <x-stat-card icon="bi-journal-check" :number="$stats['lessons_completed']" title="Lessons Completed" color="primary" />
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <x-stat-card icon="bi-translate" :number="$stats['words_learned']" title="Words Learned" color="success" />
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <x-stat-card icon="bi-clipboard-check" :number="$stats['tests_passed']" title="Tests Passed" color="info" />
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <x-stat-card icon="bi-trophy" :number="$stats['achievements_count']" title="Achievements" color="accent" />
        </div>
    </div>
@endsection
