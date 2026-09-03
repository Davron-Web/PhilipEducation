@extends('layouts.app')

@section('title', $exercise->title)
@section('page_title', 'Exercise')
@section('page_description', $exercise->type_label)

@section('content')
    @php
        $badgeClasses = match ($exercise->type_badge_color) {
            'info' => 'border border-sky/30 bg-sky/10 text-sky',
            'warning' => 'border border-sun/30 bg-sun/10 text-sun',
            'primary' => 'border border-brand/40 bg-brand/10 text-brand',
            'success' => 'border border-green-500/30 bg-green-500/10 text-green-600 dark:text-green-400',
            default => 'border border-line bg-surface2 text-ink/50',
        };
    @endphp

    <div
        class="mx-auto max-w-2xl px-4 py-10 sm:px-6 lg:px-8"
        x-data="exerciseCheck({{ Js::from(route('exercises.check', $exercise->id)) }})"
    >
        <a href="{{ route('exercises.index') }}" class="mb-4 inline-flex items-center gap-1 text-sm font-semibold text-ink/60 transition hover:text-brand">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 12H5M11 18l-6-6 6-6" /></svg>
            Все упражнения
        </a>

        <x-ui.card :hover="false" class="mb-6">
            <span class="inline-flex items-center gap-1 rounded-full px-3 py-1 text-xs font-bold {{ $badgeClasses }}">{{ $exercise->type_label }}</span>
            <h1 class="mt-2 text-2xl font-extrabold text-ink">{{ $exercise->title }}</h1>
            <p class="mt-1 text-ink/60">{{ $exercise->instructions }}</p>
        </x-ui.card>

        @if ($exercise->questions->isEmpty())
            <x-ui.card :hover="false" class="py-16 text-center">
                <p class="text-ink/50">В этом упражнении пока нет вопросов.</p>
            </x-ui.card>
        @else
            <form @submit.prevent="submit()">
                <x-ui.card :hover="false">
                    @foreach ($exercise->questions as $question)
                        <div class="mb-5">
                            <label class="mb-1.5 block font-semibold text-ink">{{ $loop->iteration }}. {{ $question->question }}</label>
                            <input
                                type="text"
                                x-model="answers[{{ $question->id }}]"
                                :disabled="checked"
                                :class="{
                                    'border-green-500 bg-green-500/5': checked && results[{{ $question->id }}] && results[{{ $question->id }}].correct,
                                    'border-red-400 bg-red-500/5': checked && results[{{ $question->id }}] && !results[{{ $question->id }}].correct,
                                    'border-line': !checked || !results[{{ $question->id }}],
                                }"
                                placeholder="Введите ответ…"
                                class="w-full rounded-lg border bg-armor2 px-4 py-2.5 text-sm text-ink placeholder:text-ink/30 transition focus:outline-none focus:border-brand focus:ring-4 focus:ring-brand/15 disabled:opacity-80"
                            >
                            <template x-if="checked && results[{{ $question->id }}] && !results[{{ $question->id }}].correct">
                                <p class="mt-1.5 text-sm font-semibold text-red-500">Правильный ответ: <span x-text="results[{{ $question->id }}].correct_answer"></span></p>
                            </template>
                            <template x-if="checked && results[{{ $question->id }}] && results[{{ $question->id }}].correct">
                                <p class="mt-1.5 text-sm font-semibold text-green-600 dark:text-green-400">Верно!</p>
                            </template>
                        </div>
                    @endforeach

                    <x-ui.button type="submit" x-show="!checked">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 6 9 17l-5-5" /></svg>
                        Проверить ответы
                    </x-ui.button>
                    <x-ui.button type="button" variant="outline" x-show="checked" style="display:none" @click="retry()">
                        Попробовать ещё раз
                    </x-ui.button>
                </x-ui.card>
            </form>

            <template x-if="suggestLesson">
                <div class="mt-6 rounded-2xl border border-sun/30 bg-sun/5 p-6" data-reveal>
                    <p class="font-bold text-ink">Похоже, эта тема даётся непросто</p>
                    <p class="mt-1 text-sm text-ink/60">
                        Уже <span x-text="suggestLesson && suggestLesson.mistake_count"></span> неверных ответов по теме «<span x-text="suggestLesson && suggestLesson.title"></span>». Рекомендуем повторить урок.
                    </p>
                    <a
                        :href="suggestLesson ? '/lessons/' + suggestLesson.id : '#'"
                        class="mt-4 inline-flex items-center gap-2 rounded-lg bg-sun px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-sun/90"
                    >
                        Повторить урок
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M13 6l6 6-6 6" /></svg>
                    </a>
                </div>
            </template>
        @endif
    </div>
@endsection

@push('scripts')
    <script>
        function exerciseCheck(checkUrl) {
            return {
                answers: {},
                results: {},
                checked: false,
                submitting: false,
                suggestLesson: null,
                submit: function () {
                    if (this.submitting) return;
                    this.submitting = true;

                    var self = this;
                    fetch(checkUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        },
                        body: JSON.stringify({ answers: this.answers }),
                    })
                        .then(function (r) { return r.json(); })
                        .then(function (data) {
                            var map = {};
                            (data.results || []).forEach(function (r) { map[r.question_id] = r; });
                            self.results = map;
                            self.checked = true;
                            self.suggestLesson = data.suggest_lesson || null;
                            self.submitting = false;
                        })
                        .catch(function () { self.submitting = false; });
                },
                retry: function () {
                    this.checked = false;
                    this.results = {};
                },
            };
        }
    </script>
@endpush
