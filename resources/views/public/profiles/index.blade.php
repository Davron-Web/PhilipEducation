@extends('layouts.app')

@section('title', 'Profile')
@section('page_title', 'My Profile')
@section('page_description', 'Your learning journey at a glance')

@section('content')
    <div class="card shadow-sm rounded-4 border-0 mb-4">
        <div class="card-body p-4">
            <div class="d-flex flex-wrap align-items-center gap-3">
                <span class="avatar-circle" style="width: 4.5rem; height: 4.5rem; font-size: 1.5rem;">
                    {{ strtoupper(substr($user->name ?? 'U', 0, 1)) }}
                </span>
                <div class="flex-grow-1">
                    <h1 class="h5 fw-bold mb-1">{{ $user->name }}</h1>
                    <p class="text-secondary mb-1"><i class="bi bi-envelope me-1"></i>{{ $user->email }}</p>
                    <x-level-badge :level="$user->level" />
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('profiles.edit') }}" class="btn btn-primary">
                        <i class="bi bi-pencil me-1"></i>Edit Profile
                    </a>
                    <a href="{{ route('profiles.edit') }}#password" class="btn btn-outline-primary">
                        <i class="bi bi-shield-lock me-1"></i>Change Password
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
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

    <div class="row g-3">
        <div class="col-12 col-sm-6">
            <div class="card shadow-sm rounded-4 border-0">
                <div class="card-body p-4 d-flex align-items-center gap-3">
                    <span class="d-inline-flex align-items-center justify-content-center rounded-3 bg-accent-subtle text-accent" style="width: 3rem; height: 3rem;">
                        <i class="bi bi-star fs-4"></i>
                    </span>
                    <div>
                        <h3 class="fw-bold mb-0">{{ $user->points }}</h3>
                        <p class="text-secondary small mb-0">Points</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6">
            <div class="card shadow-sm rounded-4 border-0">
                <div class="card-body p-4 d-flex align-items-center gap-3">
                    <span class="d-inline-flex align-items-center justify-content-center rounded-3 bg-primary-subtle text-primary" style="width: 3rem; height: 3rem;">
                        <i class="bi bi-patch-check fs-4"></i>
                    </span>
                    <div>
                        <h3 class="fw-bold mb-0">{{ $stats['certificates_count'] }}</h3>
                        <p class="text-secondary small mb-0">Certificates</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
