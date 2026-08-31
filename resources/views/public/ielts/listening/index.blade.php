{{-- IELTS Listening: список аудио-текстов (озвучены браузером) с вопросами. --}}
@extends('layouts.app')

@section('title', 'IELTS Listening')
@section('page_title', 'IELTS Listening')
@section('meta_description', 'Тренируйте IELTS Listening: слушайте текст и отвечайте на вопросы.')

@section('content')
    <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
        <a href="{{ route('ielts.index') }}" class="mb-6 inline-flex items-center gap-1 text-sm font-semibold text-ink/60 transition hover:text-sky" data-reveal>
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 12H5M11 18l-6-6 6-6" /></svg>
            IELTS
        </a>

        <div class="mb-10" data-reveal>
            <h1 class="text-3xl font-extrabold text-ink sm:text-4xl">IELTS Listening</h1>
            <p class="mt-1 text-ink/60">Прослушайте запись (озвучка браузера) и ответьте на вопросы.</p>
        </div>

        @if ($passages->isEmpty())
            <x-ui.card :hover="false" class="py-16 text-center">
                <p class="text-lg font-semibold text-ink">Записей пока нет</p>
                <p class="mt-1 text-ink/50">Загляните позже.</p>
            </x-ui.card>
        @else
            <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($passages as $passage)
                    @php $best = $bestScores->get($passage->id); @endphp
                    <a
                        href="{{ route('ielts.listening.show', $passage) }}"
                        class="group flex h-full flex-col rounded-2xl border border-white/10 bg-armor2/70 p-5 shadow-lg backdrop-blur-xl transition duration-300 ease-out hover:-translate-y-1.5 hover:border-sky/40 hover:shadow-2xl hover:shadow-sky/15"
                        data-reveal
                    >
                        <div class="mb-2 flex items-center justify-between gap-2">
                            <x-ui.badge variant="level" :level="$passage->level" />
                            @if ($best)
                                <span class="inline-flex items-center gap-1 rounded-full bg-sun/15 px-2.5 py-0.5 text-xs font-bold text-sun">{{ $best->best_score }}/{{ $best->total }}</span>
                            @endif
                        </div>
                        <h3 class="flex items-center gap-2 text-lg font-bold text-ink">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="shrink-0 text-sky"><path d="M3 18v-6a9 9 0 0 1 18 0v6" /><path d="M21 19a2 2 0 0 1-2 2h-1a2 2 0 0 1-2-2v-3a2 2 0 0 1 2-2h3ZM3 19a2 2 0 0 0 2 2h1a2 2 0 0 0 2-2v-3a2 2 0 0 0-2-2H3Z" /></svg>
                            {{ $passage->title }}
                        </h3>
                        <p class="mt-2 flex-1 text-xs font-semibold text-ink/40">{{ $passage->questions_count }} {{ $passage->questions_count === 1 ? 'вопрос' : 'вопросов' }}</p>
                        <span class="mt-4 inline-flex items-center gap-1 text-sm font-bold text-sky">
                            {{ $best ? 'Пройти снова' : 'Начать' }}
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" class="transition-transform group-hover:translate-x-1"><path d="M5 12h14M13 6l6 6-6 6" /></svg>
                        </span>
                    </a>
                @endforeach
            </div>
        @endif
    </div>
@endsection
