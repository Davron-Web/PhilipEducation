{{-- Страница задания IELTS Writing: формулировка (+график для Task 1),
     форма ответа со счётчиком слов и история попыток с оценкой Gemini. --}}
@extends('layouts.app')

@section('title', $task->title)
@section('page_title', 'IELTS Writing')
@section('meta_description', Str::limit($task->prompt, 150))

@section('content')
    <div class="mx-auto max-w-4xl px-4 py-10 sm:px-6 lg:px-8">
        <a href="{{ route('ielts.index') }}" class="mb-6 inline-flex items-center gap-1 text-sm font-semibold text-ink/60 transition hover:text-sky">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 12H5M11 18l-6-6 6-6" /></svg>
            Все задания IELTS
        </a>

        <x-ui.card :hover="false">
            <div class="mb-3 flex flex-wrap items-center gap-2">
                <span class="inline-flex items-center gap-1 rounded-full bg-{{ $task->type === 'writing_task1' ? 'sky' : 'brand' }}/10 px-2.5 py-0.5 text-xs font-bold uppercase text-{{ $task->type === 'writing_task1' ? 'sky' : 'brand' }}">
                    {{ $task->type === 'writing_task1' ? 'Writing Task 1' : 'Writing Task 2' }}
                </span>
                <span class="text-xs font-semibold text-ink/40">Минимум {{ $task->min_words }} слов</span>
            </div>
            <h1 class="text-2xl font-extrabold text-ink sm:text-3xl">{{ $task->title }}</h1>
            <p class="mt-4 leading-relaxed text-ink/80">{{ $task->prompt }}</p>
        </x-ui.card>

        @if ($task->chart_type)
            <div class="mt-6">
                <x-ielts-chart :task="$task" />
            </div>
        @endif

        <div
            class="mt-6"
            x-data="{
                text: {{ Js::from(old('answer_text', '')) }},
                get words() { return this.text.trim() === '' ? 0 : this.text.trim().split(/\s+/).length; },
            }"
        >
            <x-ui.card :hover="false">
                <form method="POST" action="{{ route('ielts.submit', $task) }}">
                    @csrf
                    <label for="answer_text" class="mb-2 block text-sm font-bold text-ink">Ваш ответ</label>
                    <textarea
                        id="answer_text"
                        name="answer_text"
                        x-model="text"
                        rows="12"
                        class="w-full rounded-xl border-2 border-white/10 bg-armor2/60 px-4 py-3 text-sm leading-relaxed text-ink placeholder:text-ink/30 backdrop-blur transition focus:border-sky focus:outline-none focus:ring-4 focus:ring-sky/15"
                        placeholder="Начните писать здесь..."
                    >{{ old('answer_text') }}</textarea>

                    <div class="mt-2 flex items-center justify-between text-xs font-semibold">
                        <span :class="words < {{ $task->min_words }} ? 'text-ink/40' : 'text-skylight'" x-text="words + ' слов'"></span>
                        <span class="text-ink/40">нужно минимум {{ $task->min_words }}</span>
                    </div>

                    @error('answer_text')
                        <p class="mt-2 text-sm font-medium text-red-400">{{ $message }}</p>
                    @enderror

                    <x-ui.button type="submit" variant="accent" class="mt-4">
                        Отправить на проверку
                    </x-ui.button>
                </form>
            </x-ui.card>
        </div>

        @if ($submissions->isNotEmpty())
            <div class="mt-10">
                <h2 class="mb-4 text-xl font-extrabold text-ink">Ваши попытки</h2>
                <div class="space-y-4">
                    @foreach ($submissions as $submission)
                        <x-ui.card :hover="false">
                            <div class="mb-3 flex flex-wrap items-center justify-between gap-2">
                                <span class="text-xs font-semibold text-ink/40">{{ $submission->created_at->translatedFormat('d M Y, H:i') }} — {{ $submission->word_count }} слов</span>
                                @if ($submission->band_score !== null)
                                    <span class="inline-flex items-center gap-1 rounded-full bg-sun/15 px-3 py-1 text-sm font-extrabold text-sun">Band {{ number_format($submission->band_score, 1) }}</span>
                                @else
                                    <span class="inline-flex items-center gap-1 rounded-full bg-ink/10 px-3 py-1 text-xs font-bold text-ink/50">Оценка не получена</span>
                                @endif
                            </div>

                            <details class="mb-3">
                                <summary class="cursor-pointer text-sm font-semibold text-sky">Показать ответ</summary>
                                <p class="mt-2 whitespace-pre-line text-sm leading-relaxed text-ink/70">{{ $submission->answer_text }}</p>
                            </details>

                            @if ($submission->feedback)
                                <div class="rounded-xl border border-sky/20 bg-sky/5 p-4">
                                    <p class="mb-1 text-xs font-bold uppercase tracking-wide text-sky">Разбор от Phil</p>
                                    <p class="whitespace-pre-line text-sm leading-relaxed text-ink/80">{{ $submission->feedback }}</p>
                                </div>
                            @endif
                        </x-ui.card>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
@endsection
