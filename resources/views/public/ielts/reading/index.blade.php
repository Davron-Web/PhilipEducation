{{-- IELTS Reading: список текстов с вопросами на понимание. --}}
@extends('layouts.app')

@section('title', 'IELTS Reading')
@section('page_title', 'IELTS Reading')
@section('meta_description', 'Тренируйте IELTS Reading: тексты и вопросы на понимание с автопроверкой.')

@section('content')
    <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
        <a href="{{ route('ielts.index') }}" class="mb-6 inline-flex items-center gap-1 text-sm font-semibold text-ink/60 transition hover:text-sky" data-reveal>
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 12H5M11 18l-6-6 6-6" /></svg>
            IELTS
        </a>

        <div class="mb-10" data-reveal>
            <h1 class="text-3xl font-extrabold text-ink sm:text-4xl">IELTS Reading</h1>
            <p class="mt-1 text-ink/60">Прочитайте текст и ответьте на вопросы — результат проверяется сразу.</p>
        </div>

        @if ($passages->isEmpty())
            <x-ui.card :hover="false" class="py-16 text-center">
                <p class="text-lg font-semibold text-ink">Текстов пока нет</p>
                <p class="mt-1 text-ink/50">Загляните позже.</p>
            </x-ui.card>
        @else
            <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($passages as $passage)
                    @php $best = $bestScores->get($passage->id); @endphp
                    <a
                        href="{{ route('ielts.reading.show', $passage) }}"
                        class="group flex h-full flex-col rounded-2xl border border-line bg-armor2/70 p-5 shadow-lg backdrop-blur-xl transition duration-300 ease-out hover:-translate-y-1.5 hover:border-skylight/40 hover:shadow-2xl hover:shadow-skylight/15"
                        data-reveal
                    >
                        <div class="mb-2 flex items-center justify-between gap-2">
                            <x-ui.badge variant="level" :level="$passage->level" />
                            @if ($best)
                                <span class="inline-flex items-center gap-1 rounded-full bg-sun/15 px-2.5 py-0.5 text-xs font-bold text-sun">{{ $best->best_score }}/{{ $best->total }}</span>
                            @endif
                        </div>
                        <h3 class="text-lg font-bold text-ink">{{ $passage->title }}</h3>
                        <p class="mt-2 line-clamp-3 flex-1 text-sm text-ink/60">{{ Str::limit(strip_tags($passage->passage_text), 130) }}</p>
                        <p class="mt-2 text-xs font-semibold text-ink/40">{{ $passage->questions_count }} {{ $passage->questions_count === 1 ? 'вопрос' : 'вопросов' }}</p>
                        <span class="mt-4 inline-flex items-center gap-1 text-sm font-bold text-skylight">
                            {{ $best ? 'Пройти снова' : 'Начать' }}
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" class="transition-transform group-hover:translate-x-1"><path d="M5 12h14M13 6l6 6-6 6" /></svg>
                        </span>
                    </a>
                @endforeach
            </div>
        @endif
    </div>
@endsection
