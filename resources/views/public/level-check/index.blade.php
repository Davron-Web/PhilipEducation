{{--
    «Определи свой уровень по тексту» — быстрая оценка через собственную
    ML-модель (ml/), в отличие от 50-вопросного placement-теста не меняет
    users.level_id, результат только копится в истории ниже.
--}}
@extends('layouts.app')

@section('title', 'Определи свой уровень по тексту')
@section('page_title', 'Level Check')

@section('content')
    <div class="mx-auto max-w-2xl px-4 py-10 sm:px-6 lg:px-8">
        <div class="mb-8 text-center" data-reveal>
            <h1 class="text-3xl font-extrabold text-ink sm:text-4xl">Определи свой уровень</h1>
            <p class="mt-1 text-ink/60">Напишите 1–5 предложений на английском — модель оценит уровень CEFR (A1–C2).</p>
        </div>

        <x-ui.card :hover="false" data-reveal>
            <form method="POST" action="{{ route('tests.level-check.check') }}" x-data="{ text: {{ Js::from(old('text', '')) }} }">
                @csrf
                <label for="text" class="mb-2 block text-sm font-bold text-ink">Ваш текст</label>
                <textarea
                    id="text"
                    name="text"
                    x-model="text"
                    rows="6"
                    maxlength="1500"
                    class="w-full rounded-xl border-2 {{ $errors->has('text') ? 'border-red-400' : 'border-line' }} bg-armor2/60 px-4 py-3 text-sm leading-relaxed text-ink placeholder:text-ink/30 backdrop-blur transition focus:border-brand focus:outline-none focus:ring-4 focus:ring-brand/15"
                    placeholder="For example: I usually get up at seven o'clock. After breakfast I go to work by bus. In the evening I like reading books or watching films with my family."
                >{{ old('text') }}</textarea>

                <div class="mt-2 flex items-center justify-between text-xs font-semibold">
                    <span class="text-ink/40" x-text="text.length + ' / 1500 символов'"></span>
                </div>

                @error('text')
                    <p class="mt-2 flex items-center gap-1 text-sm font-medium text-red-600">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" class="shrink-0"><circle cx="12" cy="12" r="10" /><path d="M12 8v5M12 16h.01" /></svg>
                        {{ $message }}
                    </p>
                @enderror

                <x-ui.button type="submit" class="mt-4 w-full">Определить уровень</x-ui.button>
            </form>
        </x-ui.card>

        @if ($history->isNotEmpty())
            <div class="mt-10" data-reveal>
                <h2 class="mb-4 text-sm font-bold uppercase tracking-wide text-ink/50">История проверок</h2>
                <x-ui.card :hover="false" class="space-y-1">
                    @foreach ($history as $check)
                        <a
                            href="{{ route('tests.level-check.result', $check) }}"
                            class="flex items-center gap-3 rounded-2xl px-3 py-2.5 transition hover:bg-surface2"
                        >
                            <x-ui.badge variant="level" :level="$check->predictedLevel" />
                            <span class="min-w-0 flex-1 truncate text-sm text-ink/70">{{ $check->input_text }}</span>
                            <span class="shrink-0 text-xs text-ink/40">{{ $check->created_at->diffForHumans() }}</span>
                        </a>
                    @endforeach
                </x-ui.card>
            </div>
        @endif
    </div>
@endsection
