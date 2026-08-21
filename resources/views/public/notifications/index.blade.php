@extends('layouts.app')

@section('title', 'Notifications')
@section('page_title', 'Notifications')
@section('page_description', $notifications->total() . ' total')

@section('content')
    <div class="d-flex justify-content-end mb-3">
        <form method="POST" action="{{ route('notifications.markAllRead') }}">
            @csrf
            <button type="submit" class="btn btn-outline-primary btn-sm">
                <i class="bi bi-check2-all me-1"></i>Mark all as read
            </button>
        </form>
    </div>

    @if($notifications->isEmpty())
        <div class="text-center py-5">
            <i class="bi bi-bell-slash display-4 text-secondary"></i>
            <p class="text-secondary mt-3 mb-0">You don't have any notifications yet.</p>
        </div>
    @else
        <div class="list-group shadow-sm rounded-4 overflow-hidden mb-3">
            @foreach($notifications as $notification)
                <a href="{{ route('notifications.show', $notification->id) }}"
                   class="list-group-item list-group-item-action d-flex align-items-start gap-3 p-3 {{ $notification->is_read ? '' : 'bg-primary-subtle' }}">
                    <span class="d-inline-flex align-items-center justify-content-center rounded-circle bg-primary-subtle text-primary flex-shrink-0"
                          style="width: 2.5rem; height: 2.5rem;">
                        <i class="bi bi-bell"></i>
                    </span>
                    <div class="flex-grow-1">
                        <div class="d-flex align-items-center justify-content-between">
                            <span class="fw-semibold">{{ $notification->title }}</span>
                            <span class="text-secondary small">{{ $notification->created_at->diffForHumans() }}</span>
                        </div>
                        <p class="text-secondary small mb-0">{{ Str::limit($notification->message, 120) }}</p>
                    </div>
                    @if(!$notification->is_read)
                        <span class="badge text-bg-primary rounded-pill align-self-center">New</span>
                    @endif
                </a>
            @endforeach
        </div>

        {{ $notifications->links() }}
    @endif
@endsection
