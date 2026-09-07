@extends('layouts.app')

@section('title', $test->title)
@section('page_title', 'Test')
@section('page_description', optional(optional($test->lesson)->level)->name ?? 'Self-check quiz')

@section('content')
    <div class="mx-auto max-w-3xl px-4 py-10 sm:px-6 lg:px-8">
        <a href="{{ route('tests.index') }}" class="mb-4 inline-flex items-center gap-1 text-sm font-semibold text-ink/60 transition hover:text-brand">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 12H5M11 18l-6-6 6-6" /></svg>
            Все тесты
        </a>

        <x-ui.card :hover="false" class="mb-6">
            <div class="mb-2 flex flex-wrap items-center gap-2">
                <x-ui.badge variant="level" :level="optional($test->lesson)->level" />
                <span class="inline-flex items-center gap-1 text-sm text-ink/50">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10" /><path d="M12 16v-4M12 8h.01" /></svg>
                    {{ $test->questions->count() }} вопросов
                </span>
                @if ($test->time_limit)
                    <span class="inline-flex items-center gap-1 text-sm text-ink/50">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10" /><polyline points="12 6 12 12 16 14" /></svg>
                        {{ $test->time_limit }} мин
                    </span>
                @endif
                <span class="inline-flex items-center gap-1 text-sm text-ink/50">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 11l3 3L22 4" /><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11" /></svg>
                    Проходной балл: {{ $test->passing_score }}%
                </span>
            </div>
            <h1 class="text-2xl font-extrabold text-ink">{{ $test->title }}</h1>
        </x-ui.card>

        @if ($test->questions->isEmpty())
            <x-ui.card :hover="false" class="py-16 text-center">
                <p class="text-ink/50">В этом тесте пока нет вопросов.</p>
            </x-ui.card>
        @else
            @php $saved = $draft?->answers ?? []; @endphp

            @if ($draft && $draft->answeredCount() > 0)
                <div class="mb-5 flex flex-wrap items-center justify-between gap-3 rounded-2xl border border-sun/30 bg-sun/5 px-5 py-3.5">
                    <p class="text-sm text-ink/70">
                        Вы уже отвечали на этот тест — {{ $draft->answeredCount() }} из {{ $test->questions->count() }}.
                        Ответы восстановлены.
                    </p>
                    <form method="POST" action="{{ route('tests.draft.discard', $test->id) }}">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-xs font-bold uppercase tracking-wider text-ink/50 underline hover:text-brand">
                            Начать заново
                        </button>
                    </form>
                </div>
            @endif

            <form
                method="POST"
                action="{{ route('tests.submit', $test->id) }}"
                x-data="testForm({{ (int) ($draft?->seconds_spent ?? 0) }}, '{{ route('tests.draft.save', $test->id) }}')"
                @change="saveDraft()"
            >
                @csrf
                {{-- Сколько времени занял тест — уходит в историю попыток. --}}
                <input type="hidden" name="duration_seconds" x-bind:value="elapsed()">

                <x-ui.card :hover="false">
                    @foreach ($test->questions as $question)
                        <div class="mb-6 border-b border-line pb-6 last:border-0 last:pb-0">
                            <p class="mb-3 font-semibold text-ink">
                                {{ $loop->iteration }}. {{ $question->question }}
                                @if ($question->type === 'multiple_choice')
                                    <span class="ml-1 text-xs font-normal text-ink/40">(несколько вариантов)</span>
                                @endif
                            </p>

                            @if ($question->answers->isNotEmpty() && $question->type !== 'text')
                                <div class="space-y-2">
                                    @foreach ($question->answers as $answer)
                                        {{-- min-h-12 = 48px: по вариантам нажимают десятки раз подряд, и
                                             прежние 42px на телефоне давали промахи. Нажимается
                                             вся строка — input лежит внутри label. --}}
                                        <label for="answer-{{ $answer->id }}" class="flex min-h-12 cursor-pointer items-center gap-3 rounded-xl border border-line bg-armor px-4 py-3 text-sm text-ink transition hover:border-brand/40 has-[:checked]:border-brand has-[:checked]:bg-brand/5">
                                            <input
                                                class="h-5 w-5 shrink-0 accent-brand"
                                                type="{{ $question->type === 'multiple_choice' ? 'checkbox' : 'radio' }}"
                                                name="answers[{{ $question->id }}]{{ $question->type === 'multiple_choice' ? '[]' : '' }}"
                                                id="answer-{{ $answer->id }}"
                                                value="{{ $answer->id }}"
                                                @checked(in_array((string) $answer->id, (array) ($saved[$question->id] ?? []), true) || (string) ($saved[$question->id] ?? null) === (string) $answer->id)
                                            >
                                            {{ $answer->answer }}
                                        </label>
                                    @endforeach
                                </div>
                            @else
                                <x-ui.input type="text" :name="'answers['.$question->id.']'" :value="is_string($saved[$question->id] ?? null) ? $saved[$question->id] : ''" placeholder="Ваш ответ…" />
                            @endif
                        </div>
                    @endforeach

                    <x-ui.button type="submit">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M4 15s1-1 4-1 5 2 8 2 4-1 4-1V3s-1 1-4 1-5-2-8-2-4 1-4 1z" /><path d="M4 22V15" /></svg>
                        Завершить тест
                    </x-ui.button>
                </x-ui.card>
            </form>
        @endif
    </div>
@endsection

@push('scripts')
<script>
function testForm(alreadySpent, draftUrl) {
    return {
        started: Date.now(),
        alreadySpent: alreadySpent,
        saving: false,
        pending: false,

        // Время копится между заходами: вернувшийся ученик не должен
        // выглядеть так, будто прошёл тест за минуту.
        elapsed: function () {
            return this.alreadySpent + Math.round((Date.now() - this.started) / 1000);
        },

        saveDraft: function () {
            // Пока запрос в пути, следующий не шлём — просто отметим, что
            // нужно повторить: иначе быстрые клики дают очередь запросов.
            if (this.saving) {
                this.pending = true;

                return;
            }

            var self = this;
            this.saving = true;

            var form = this.$el;
            var data = new FormData(form);
            data.delete('_token');
            data.delete('duration_seconds');
            data.append('seconds_spent', this.elapsed());

            fetch(draftUrl, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                },
                body: data,
            })
                .catch(function () { /* черновик — не то, ради чего стоит тревожить ученика */ })
                .finally(function () {
                    self.saving = false;

                    if (self.pending) {
                        self.pending = false;
                        self.saveDraft();
                    }
                });
        },
    };
}
</script>
@endpush
