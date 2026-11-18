{{-- Тест на определение уровня: вступительная страница. --}}
@extends('layouts.app')

@section('title', 'Определить свой уровень')
@section('page_title', 'Placement Test')
@section('meta_description', 'Пройдите тест из 50 вопросов и узнайте свой точный уровень английского — от A1 до C2.')

@section('content')
    <div class="mx-auto max-w-2xl px-4 py-10 sm:px-6 lg:px-8">
        <x-ui.card :hover="false" class="text-center">
            <span class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-brand/10 text-brand">
                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M9 11l3 3L22 4" /><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11" /></svg>
            </span>

            <h1 class="font-display text-3xl font-semibold text-ink">Определите свой уровень</h1>
            <p class="mx-auto mt-3 max-w-md text-ink/60">
                {{ $questionsCount }} вопросов по грамматике и лексике — от базовых форм (A1)
                до тонкостей продвинутого английского (C2). Отвечайте честно и не
                торопитесь: результат станет вашим уровнем в личном кабинете
                и по нему подберутся уроки.
            </p>

            <ul class="mx-auto mt-6 max-w-sm space-y-2 text-left text-sm text-ink/70">
                <li class="flex items-start gap-2">
                    <svg class="mt-0.5 shrink-0 text-brand" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 6 9 17l-5-5" /></svg>
                    Таймера на вопрос нет — важна точность, а не скорость.
                </li>
                <li class="flex items-start gap-2">
                    <svg class="mt-0.5 shrink-0 text-brand" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 6 9 17l-5-5" /></svg>
                    Вопросы идут по возрастанию сложности — если совсем не
                    знаете ответ, отмечайте наугад и двигайтесь дальше.
                </li>
                <li class="flex items-start gap-2">
                    <svg class="mt-0.5 shrink-0 text-brand" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 6 9 17l-5-5" /></svg>
                    Пройти можно заново в любой момент — учитывается
                    последний результат.
                </li>
            </ul>

            @if ($lastAttempt)
                <div class="mx-auto mt-6 max-w-sm rounded-xl border border-line bg-armor px-4 py-3 text-sm text-ink/60">
                    Последний раз вы прошли тест
                    {{ $lastAttempt->completed_at->translatedFormat('d MMMM Y') }}
                    — результат: <x-ui.badge variant="level" :level="$lastAttempt->resultLevel" />
                </div>
            @endif

            <form method="POST" action="{{ route('tests.placement.start') }}" class="mt-6">
                @csrf
                <x-ui.button type="submit" size="lg">
                    {{ $lastAttempt ? 'Пройти заново' : 'Начать тест' }}
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M13 6l6 6-6 6" /></svg>
                </x-ui.button>
            </form>
        </x-ui.card>
    </div>
@endsection
