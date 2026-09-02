@extends('layouts.app')

@section('title', $notification->title)
@section('page_title', 'Notification')
@section('page_description', $notification->created_at->diffForHumans())

@section('content')
    <div class="mx-auto max-w-2xl px-4 py-10 sm:px-6 lg:px-8">
        <a href="{{ route('notifications.index') }}" class="mb-4 inline-flex items-center gap-1 text-sm font-semibold text-ink/60 transition hover:text-brand">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 12H5M11 18l-6-6 6-6" /></svg>
            Все уведомления
        </a>

        <x-ui.card :hover="false">
            <div class="mb-4 flex items-center gap-3">
                <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-brand/10 text-brand">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8a6 6 0 0 0-12 0c0 7-3 9-3 9h18s-3-2-3-9" /><path d="M13.73 21a2 2 0 0 1-3.46 0" /></svg>
                </span>
                <div>
                    <h1 class="text-lg font-extrabold text-ink">{{ $notification->title }}</h1>
                    <span class="text-sm text-ink/40">{{ $notification->created_at->format('M j, Y \a\t H:i') }}</span>
                </div>
            </div>
            <p class="text-ink/70">{{ $notification->message }}</p>
        </x-ui.card>
    </div>
@endsection
