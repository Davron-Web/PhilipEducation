@extends('layouts.app')

@section('title', $user->name)
@section('page_title', 'Profile')
@section('page_description', 'Student profile')

@section('content')
    <div class="mx-auto max-w-4xl px-4 py-10 sm:px-6 lg:px-8">
        <x-ui.card :hover="false" class="mb-6">
            <div class="flex flex-wrap items-center gap-4">
                <span class="flex h-[4.5rem] w-[4.5rem] shrink-0 items-center justify-center rounded-full bg-gradient-to-br from-brand to-sky text-2xl font-extrabold text-white">
                    {{ strtoupper(substr($user->name ?? 'U', 0, 1)) }}
                </span>
                <div>
                    <h1 class="text-lg font-extrabold text-ink">{{ $user->name }}</h1>
                    <div class="mt-1">
                        <x-ui.badge variant="level" :level="$user->level" />
                    </div>
                </div>
            </div>
        </x-ui.card>

        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <x-stat-card icon="bi-journal-check" :number="$stats['lessons_completed']" title="Уроков пройдено" color="primary" />
            <x-stat-card icon="bi-translate" :number="$stats['words_learned']" title="Слов выучено" color="success" />
            <x-stat-card icon="bi-clipboard-check" :number="$stats['tests_passed']" title="Тестов сдано" color="info" />
            <x-stat-card icon="bi-trophy" :number="$stats['achievements_count']" title="Достижений" color="accent" />
        </div>
    </div>
@endsection
