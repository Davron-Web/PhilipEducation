@extends('layouts.app')

@section('title', 'Profile')
@section('page_title', 'My Profile')
@section('page_description', 'Your learning journey at a glance')

@section('content')
    <div class="mx-auto max-w-4xl px-4 py-10 sm:px-6 lg:px-8">
        <x-ui.card :hover="false" class="mb-6">
            <div class="flex flex-wrap items-center gap-4">
                <span class="flex h-[4.5rem] w-[4.5rem] shrink-0 items-center justify-center overflow-hidden rounded-full bg-gradient-to-br from-brand to-sky text-2xl font-extrabold text-white">
                    @if ($user->avatarUrl())
                        <img src="{{ $user->avatarUrl() }}" alt="Аватар" class="h-full w-full object-cover">
                    @else
                        {{ $user->initial() }}
                    @endif
                </span>
                <div class="flex-1">
                    <h1 class="text-lg font-extrabold text-ink">{{ $user->name }}</h1>
                    <p class="mt-0.5 flex items-center gap-1 text-sm text-ink/50">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="4" width="20" height="16" rx="2" /><path d="m2 7 10 6 10-6" /></svg>
                        {{ $user->email }}
                    </p>
                    <div class="mt-2">
                        <x-ui.badge variant="level" :level="$user->level" />
                    </div>
                </div>
                <div class="flex gap-2">
                    <x-ui.button :href="route('profiles.edit')" size="sm">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7" /><path d="M18.5 2.5a2.12 2.12 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z" /></svg>
                        Редактировать
                    </x-ui.button>
                    <x-ui.button :href="route('profiles.edit').'#password-section'" variant="outline" size="sm">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="11" width="18" height="11" rx="2" /><path d="M7 11V7a5 5 0 0 1 10 0v4" /></svg>
                        Сменить пароль
                    </x-ui.button>
                </div>
            </div>
        </x-ui.card>

        <div class="mb-6 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <x-stat-card icon="bi-journal-check" :number="$stats['lessons_completed']" title="Уроков пройдено" color="primary" />
            <x-stat-card icon="bi-translate" :number="$stats['words_learned']" title="Слов выучено" color="success" />
            <x-stat-card icon="bi-clipboard-check" :number="$stats['tests_passed']" title="Тестов сдано" color="info" />
            <x-stat-card icon="bi-trophy" :number="$stats['achievements_count']" title="Достижений" color="accent" />
        </div>

        <div class="grid gap-4 sm:grid-cols-2">
            <x-ui.card :hover="false" class="flex items-center gap-3">
                <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-sun/10 text-sun">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2" /></svg>
                </span>
                <div>
                    <h3 class="text-xl font-extrabold text-ink">{{ $user->points }}</h3>
                    <p class="text-sm text-ink/50">Очков</p>
                </div>
            </x-ui.card>
            <x-ui.card :hover="false" class="flex items-center gap-3">
                <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-brand/10 text-brand">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" /><path d="m9 12 2 2 4-4" /></svg>
                </span>
                <div>
                    <h3 class="text-xl font-extrabold text-ink">{{ $stats['certificates_count'] }}</h3>
                    <p class="text-sm text-ink/50">Сертификатов</p>
                </div>
            </x-ui.card>
        </div>
    </div>
@endsection
