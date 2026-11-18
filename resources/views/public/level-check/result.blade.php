{{-- Итог проверки уровня по тексту — вероятности от собственной ML-модели (ml/). --}}
@extends('layouts.app')

@section('title', 'Ваш уровень английского')
@section('page_title', 'Level Check')

@php
    $levels = ['A1', 'A2', 'B1', 'B2', 'C1', 'C2'];
@endphp

@section('content')
    <div class="mx-auto max-w-2xl px-4 py-10 sm:px-6 lg:px-8">
        <x-ui.card :hover="false" class="text-center">
            <p class="text-sm font-bold uppercase tracking-wide text-ink/50">Предполагаемый уровень</p>
            <p class="mt-2 font-display text-6xl font-bold text-brand">{{ $levelCheck->predictedLevel->code }}</p>
            <p class="mt-1 text-lg font-semibold text-ink">{{ $levelCheck->predictedLevel->name }}</p>

            <p class="mx-auto mt-4 max-w-md text-xs text-ink/40">
                Это быстрая неофициальная оценка от собственной ML-модели по
                короткому тексту — в отличие от полного теста на определение
                уровня, она не сохраняется как ваш уровень в профиле.
            </p>
        </x-ui.card>

        <x-ui.card :hover="false" class="mt-6">
            <h2 class="mb-4 text-sm font-bold uppercase tracking-wide text-ink/50">Вероятность по уровням</h2>
            <div class="space-y-3">
                @foreach ($levels as $code)
                    @php $probability = (float) ($levelCheck->probabilities[$code] ?? 0); @endphp
                    <div class="flex items-center gap-3">
                        <span class="w-9 shrink-0 text-sm font-bold text-ink/70">{{ $code }}</span>
                        <div class="h-2.5 flex-1 overflow-hidden rounded-full bg-surface2">
                            <div
                                class="h-full rounded-full {{ $code === $levelCheck->predictedLevel->code ? 'bg-brand' : 'bg-ink/20' }}"
                                style="width: {{ round($probability * 100) }}%"
                            ></div>
                        </div>
                        <span class="w-12 shrink-0 text-right text-sm text-ink/50">{{ round($probability * 100) }}%</span>
                    </div>
                @endforeach
            </div>
        </x-ui.card>

        <x-ui.card :hover="false" class="mt-6">
            <h2 class="mb-2 text-sm font-bold uppercase tracking-wide text-ink/50">Ваш текст</h2>
            <p class="whitespace-pre-line text-sm text-ink/70">{{ $levelCheck->input_text }}</p>
        </x-ui.card>

        <div class="mt-6 flex flex-wrap items-center justify-center gap-3">
            <x-ui.button href="{{ route('tests.level-check.index') }}">Проверить ещё раз</x-ui.button>
            <x-ui.button href="{{ route('lessons.index', ['level' => $levelCheck->predictedLevel->code]) }}" variant="outline">
                Уроки уровня {{ $levelCheck->predictedLevel->code }}
            </x-ui.button>
        </div>
    </div>
@endsection
