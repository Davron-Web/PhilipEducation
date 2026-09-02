@extends('layouts.app')

@section('title', 'Notifications')
@section('page_title', 'Notifications')
@section('page_description', $notifications->total() . ' total')

@section('content')
    <div class="mx-auto max-w-2xl px-4 py-10 sm:px-6 lg:px-8">
        <div class="mb-6 flex items-center justify-between" data-reveal>
            <h1 class="text-2xl font-extrabold text-ink">Уведомления</h1>
            <form method="POST" action="{{ route('notifications.markAllRead') }}">
                @csrf
                <x-ui.button type="submit" variant="outline" size="sm">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M18 6 7 17l-5-5" /><path d="m22 10-7.5 7.5L13 16" /></svg>
                    Отметить все как прочитанные
                </x-ui.button>
            </form>
        </div>

        @if ($notifications->isEmpty())
            <x-ui.card :hover="false" class="py-16 text-center">
                <p class="text-ink/50">У вас пока нет уведомлений.</p>
            </x-ui.card>
        @else
            <div class="mb-4 overflow-hidden rounded-2xl border border-line shadow-soft">
                @foreach ($notifications as $notification)
                    <a
                        href="{{ route('notifications.show', $notification->id) }}"
                        class="flex items-start gap-3 border-b border-line bg-armor2 p-4 transition last:border-0 hover:bg-surface2 {{ $notification->is_read ? '' : 'bg-brand/5' }}"
                    >
                        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-brand/10 text-brand">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8a6 6 0 0 0-12 0c0 7-3 9-3 9h18s-3-2-3-9" /><path d="M13.73 21a2 2 0 0 1-3.46 0" /></svg>
                        </span>
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center justify-between gap-2">
                                <span class="font-semibold text-ink">{{ $notification->title }}</span>
                                <span class="shrink-0 text-xs text-ink/40">{{ $notification->created_at->diffForHumans() }}</span>
                            </div>
                            <p class="mt-0.5 text-sm text-ink/50">{{ Str::limit($notification->message, 120) }}</p>
                        </div>
                        @if (! $notification->is_read)
                            <span class="mt-1 h-2 w-2 shrink-0 rounded-full bg-brand"></span>
                        @endif
                    </a>
                @endforeach
            </div>

            {{ $notifications->links() }}
        @endif
    </div>
@endsection
