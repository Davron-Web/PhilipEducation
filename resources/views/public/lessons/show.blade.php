@extends('layouts.app')

@section('title', $lesson->title)
@section('page_title', 'Lesson')
@section('page_description', optional($lesson->level)->name ?? 'Lesson details')

@section('content')
    <div class="mx-auto max-w-4xl px-4 py-10 sm:px-6 lg:px-8">
        <a href="{{ route('lessons.index') }}" class="mb-4 inline-flex items-center gap-1 text-sm font-semibold text-ink/60 transition hover:text-brand">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 12H5M11 18l-6-6 6-6" /></svg>
            Все уроки
        </a>

        <x-ui.card :hover="false" class="mb-6">
            <div class="mb-2 flex flex-wrap items-center gap-2">
                <x-ui.badge variant="level" :level="$lesson->level" />
                <span class="inline-flex items-center gap-1 text-sm text-ink/50">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10" /><polyline points="12 6 12 12 16 14" /></svg>
                    {{ $lesson->estimated_minutes }} мин
                </span>
            </div>
            <h1 class="text-2xl font-extrabold text-ink">{{ $lesson->title }}</h1>
            <p class="mt-2 text-ink/60">{{ $lesson->description }}</p>

            <div class="mt-4">
                @if ($progress && $progress->is_completed)
                    <x-ui.badge variant="success">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 6 9 17l-5-5" /></svg>
                        Пройден
                    </x-ui.badge>
                @else
                    <form method="POST" action="{{ route('public.lessons.complete', $lesson->id) }}">
                        @csrf
                        <x-ui.button type="submit">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 6 9 17l-5-5" /></svg>
                            Отметить как пройденный
                        </x-ui.button>
                    </form>
                @endif
            </div>
        </x-ui.card>

        @if ($lesson->contents->isNotEmpty())
            <h2 class="mb-3 text-lg font-extrabold text-ink">Содержание урока</h2>
            <div class="mb-8 space-y-3" x-data="{ open: 0 }">
                @foreach ($lesson->contents as $content)
                    <x-ui.card :hover="false" class="!p-0 overflow-hidden">
                        <button
                            type="button"
                            @click="open = (open === {{ $loop->index }} ? -1 : {{ $loop->index }})"
                            class="flex w-full items-center justify-between gap-3 px-6 py-4 text-left font-bold text-ink transition hover:text-brand"
                        >
                            {{ $content->title ?: 'Раздел '.$loop->iteration }}
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" class="shrink-0 transition-transform" :class="{ 'rotate-180': open === {{ $loop->index }} }"><path d="m6 9 6 6 6-6" /></svg>
                        </button>
                        <div x-show="open === {{ $loop->index }}" style="display:none" x-cloak class="border-t border-line px-6 py-4">
                            <div class="lesson-content-body max-w-none text-[15px] leading-relaxed text-ink/80 [&_h2:first-child]:mt-0 [&_h2]:mb-3 [&_h2]:mt-6 [&_h2]:text-lg [&_h2]:font-bold [&_h2]:text-ink [&_li]:mb-1.5 [&_p]:mb-3 [&_strong]:font-semibold [&_strong]:text-ink [&_table]:w-full [&_table]:border-collapse [&_td]:border [&_td]:border-line [&_td]:p-2 [&_th]:border [&_th]:border-line [&_th]:bg-surface2 [&_th]:p-2 [&_th]:font-semibold [&_ul]:list-disc [&_ul]:pl-5">
                                {!! $content->content !!}
                            </div>
                            @if ($content->file_url)
                                <a href="{{ $content->file_url }}" target="_blank" class="mt-2 inline-flex items-center gap-1 text-sm font-semibold text-brand hover:underline">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21.44 11.05 12.25 20.24a5 5 0 0 1-7.07-7.07l9.19-9.19a3.5 3.5 0 0 1 4.95 4.95L10.13 17.1a2 2 0 0 1-2.83-2.83l8.49-8.48" /></svg>
                                    Вложение
                                </a>
                            @endif
                        </div>
                    </x-ui.card>
                @endforeach
            </div>
        @endif

        @if ($lesson->words->isNotEmpty())
            <h2 class="mb-3 text-lg font-extrabold text-ink">Слова из урока</h2>
            <div class="mb-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($lesson->words as $word)
                    <x-word-card :word="$word" :learned="false" />
                @endforeach
            </div>
        @endif

        @if ($lesson->tests->isNotEmpty())
            <h2 class="mb-3 text-lg font-extrabold text-ink">Тесты по теме</h2>
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($lesson->tests as $test)
                    <x-test-card :test="$test" />
                @endforeach
            </div>
        @endif
    </div>
@endsection
