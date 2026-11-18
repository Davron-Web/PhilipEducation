@extends('layouts.app')

@section('title', 'Чат')
@section('page_title', 'Общий чат')
@section('meta_description', 'Общий чат Philip Education — общайтесь с другими учениками платформы.')

{{-- chat.js подключается глобально вместе с x-chat-drawer (layouts/app.blade.php),
     отдельный @push('scripts') здесь больше не нужен. --}}

@section('content')
    <div class="mx-auto max-w-3xl px-4 py-10 sm:px-6 lg:px-8">
        <div class="mb-8" data-reveal>
            <h1 class="text-3xl font-extrabold text-ink sm:text-4xl">Общий чат</h1>
            <p class="mt-1 text-ink/60">Одна комната для всех учеников платформы — будьте вежливы.</p>
        </div>

        <div class="flex h-[60vh] min-h-[500px] flex-col overflow-hidden rounded-2xl border border-line bg-armor2 shadow-soft">
            <x-chat.feed
                :initial-messages="$initialMessages"
                :oldest-id="$oldestId"
                :has-more-history="$hasMoreHistory"
                :is-banned="$isBanned"
            />
        </div>
    </div>
@endsection
