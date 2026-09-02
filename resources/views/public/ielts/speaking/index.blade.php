{{-- IELTS Speaking: cue card'ы для самостоятельной практики с таймером. --}}
@extends('layouts.app')

@section('title', 'IELTS Speaking')
@section('page_title', 'IELTS Speaking')
@section('meta_description', 'Тренируйте IELTS Speaking Part 2: cue card с таймером подготовки и ответа.')

@section('content')
    <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
        <a href="{{ route('ielts.index') }}" class="mb-6 inline-flex items-center gap-1 text-sm font-semibold text-ink/60 transition hover:text-sun" data-reveal>
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 12H5M11 18l-6-6 6-6" /></svg>
            IELTS
        </a>

        <div class="mb-10" data-reveal>
            <h1 class="text-3xl font-extrabold text-ink sm:text-4xl">IELTS Speaking</h1>
            <p class="mt-1 text-ink/60">Part 2 (cue card): 1 минута на подготовку, 1–2 минуты на ответ. Без записи — для самостоятельной практики вслух.</p>
        </div>

        @if ($cards->isEmpty())
            <x-ui.card :hover="false" class="py-16 text-center">
                <p class="text-lg font-semibold text-ink">Карточек пока нет</p>
                <p class="mt-1 text-ink/50">Загляните позже.</p>
            </x-ui.card>
        @else
            <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($cards as $card)
                    <a
                        href="{{ route('ielts.speaking.show', $card) }}"
                        class="group flex h-full flex-col rounded-2xl border border-line bg-armor2/70 p-5 shadow-lg backdrop-blur-xl transition duration-300 ease-out hover:-translate-y-1.5 hover:border-sun/40 hover:shadow-2xl hover:shadow-sun/15"
                        data-reveal
                    >
                        @if ($card->topic)
                            <span class="mb-2 inline-flex w-fit items-center gap-1 rounded-full bg-sun/10 px-2.5 py-0.5 text-xs font-bold text-sun">{{ $card->topic }}</span>
                        @endif
                        <h3 class="text-lg font-bold text-ink">{{ $card->title }}</h3>
                        <p class="mt-2 line-clamp-3 flex-1 text-sm text-ink/60">{{ $card->prompt }}</p>
                        <span class="mt-4 inline-flex items-center gap-1 text-sm font-bold text-sun">
                            Начать
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" class="transition-transform group-hover:translate-x-1"><path d="M5 12h14M13 6l6 6-6 6" /></svg>
                        </span>
                    </a>
                @endforeach
            </div>
        @endif
    </div>
@endsection
