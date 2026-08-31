{{-- IELTS: точка входа — 4 карточки по навыкам (Listening/Reading/Writing/Speaking). --}}
@extends('layouts.app')

@section('title', 'IELTS')
@section('page_title', 'Подготовка к IELTS')
@section('meta_description', 'Готовьтесь к IELTS: Listening, Reading, Writing и Speaking с проверкой и обратной связью.')

@section('content')
    <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
        <div class="mb-10" data-reveal>
            <h1 class="text-3xl font-extrabold text-ink sm:text-4xl">IELTS</h1>
            <p class="mt-1 text-ink/60">Выберите навык — задания в формате настоящего экзамена.</p>
        </div>

        <div class="grid gap-5 sm:grid-cols-2">
            <a
                href="{{ route('ielts.listening.index') }}"
                class="group flex items-center gap-4 rounded-2xl border border-white/10 bg-gradient-to-br from-sky to-skylight p-6 text-armor shadow-lg shadow-sky/20 transition duration-300 ease-out hover:-translate-y-1.5 hover:shadow-2xl"
                data-reveal
            >
                <span class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-white/25">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 18v-6a9 9 0 0 1 18 0v6" /><path d="M21 19a2 2 0 0 1-2 2h-1a2 2 0 0 1-2-2v-3a2 2 0 0 1 2-2h3ZM3 19a2 2 0 0 0 2 2h1a2 2 0 0 0 2-2v-3a2 2 0 0 0-2-2H3Z" /></svg>
                </span>
                <span>
                    <span class="block text-xl font-extrabold">Listening</span>
                    <span class="block text-sm font-semibold opacity-80">{{ $counts['listening'] }} {{ $counts['listening'] === 1 ? 'запись' : 'записей' }} — слушайте и отвечайте на вопросы</span>
                </span>
            </a>

            <a
                href="{{ route('ielts.reading.index') }}"
                class="group flex items-center gap-4 rounded-2xl border border-white/10 bg-gradient-to-br from-skylight to-sky p-6 text-armor shadow-lg shadow-skylight/20 transition duration-300 ease-out hover:-translate-y-1.5 hover:shadow-2xl"
                data-reveal
            >
                <span class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-white/25">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20M4 19.5A2.5 2.5 0 0 0 6.5 22H20V4H6.5A2.5 2.5 0 0 0 4 6.5v13Z" /></svg>
                </span>
                <span>
                    <span class="block text-xl font-extrabold">Reading</span>
                    <span class="block text-sm font-semibold opacity-80">{{ $counts['reading'] }} {{ $counts['reading'] === 1 ? 'текст' : 'текстов' }} — читайте и отвечайте на вопросы</span>
                </span>
            </a>

            <a
                href="{{ route('ielts.writing.index') }}"
                class="group flex items-center gap-4 rounded-2xl border border-white/10 bg-gradient-to-br from-brand to-sky p-6 text-white shadow-lg shadow-brand/20 transition duration-300 ease-out hover:-translate-y-1.5 hover:shadow-2xl"
                data-reveal
            >
                <span class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-white/20">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4Z" /></svg>
                </span>
                <span>
                    <span class="block text-xl font-extrabold">Writing</span>
                    <span class="block text-sm font-semibold opacity-80">{{ $counts['writing'] }} заданий — Task 1 (график) и Task 2 (эссе), проверка от ИИ</span>
                </span>
            </a>

            <a
                href="{{ route('ielts.speaking.index') }}"
                class="group flex items-center gap-4 rounded-2xl border border-white/10 bg-gradient-to-br from-sun to-amber-500 p-6 text-armor shadow-lg shadow-sun/20 transition duration-300 ease-out hover:-translate-y-1.5 hover:shadow-2xl"
                data-reveal
            >
                <span class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-white/25">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="9" y="2" width="6" height="12" rx="3" /><path d="M5 10v1a7 7 0 0 0 14 0v-1M12 18v4M8 22h8" /></svg>
                </span>
                <span>
                    <span class="block text-xl font-extrabold">Speaking</span>
                    <span class="block text-sm font-semibold opacity-80">{{ $counts['speaking'] }} {{ $counts['speaking'] === 1 ? 'карточка' : 'карточек' }} — cue card с таймером подготовки и ответа</span>
                </span>
            </a>
        </div>
    </div>
@endsection
