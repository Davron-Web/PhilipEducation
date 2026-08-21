@extends('layouts.app')

@section('title', $notification->title)
@section('page_title', 'Notification')
@section('page_description', $notification->created_at->diffForHumans())

@section('content')
    <div class="mb-3">
        <a href="{{ route('notifications.index') }}" class="link-secondary text-decoration-none small">
            <i class="bi bi-arrow-left me-1"></i>Back to notifications
        </a>
    </div>

    <div class="card shadow-sm rounded-4 border-0">
        <div class="card-body p-4">
            <div class="d-flex align-items-center gap-3 mb-3">
                <span class="d-inline-flex align-items-center justify-content-center rounded-circle bg-primary-subtle text-primary"
                      style="width: 3rem; height: 3rem;">
                    <i class="bi bi-bell fs-5"></i>
                </span>
                <div>
                    <h1 class="h5 fw-bold mb-0">{{ $notification->title }}</h1>
                    <span class="text-secondary small">{{ $notification->created_at->format('M j, Y \a\t H:i') }}</span>
                </div>
            </div>
            <p class="mb-0">{{ $notification->message }}</p>
        </div>
    </div>
@endsection
