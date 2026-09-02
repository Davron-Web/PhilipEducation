@extends('layouts.app')

@section('title', 'Exercises')
@section('page_title', 'Exercises')
@section('page_description', $exercises->count() . ' exercises available')

@section('content')
    <div class="mx-auto max-w-4xl px-4 py-10 sm:px-6 lg:px-8">
        <div class="mb-8" data-reveal>
            <h1 class="text-3xl font-extrabold text-ink sm:text-4xl">Упражнения</h1>
            <p class="mt-1 text-ink/60">{{ $exercises->count() }} упражнений доступно.</p>
        </div>

        @if ($exercises->isEmpty())
            <x-ui.card :hover="false" class="py-16 text-center">
                <p class="text-ink/50">Упражнения пока не опубликованы. Загляните позже!</p>
            </x-ui.card>
        @else
            @php
                $byLevel = $exercises->groupBy(fn ($exercise) => optional(optional($exercise->lesson)->level)->id ?? 0);
                $moduleIndex = 0;
            @endphp

            <div class="space-y-5">
                @foreach ($byLevel as $levelId => $levelExercises)
                    @php
                        $level = optional($levelExercises->first()->lesson)->level;
                        $moduleIndex++;
                    @endphp
                    <x-ui.card :hover="false">
                        <p class="text-xs font-bold uppercase tracking-widest text-brand">Модуль {{ $moduleIndex }}{{ $level ? ' · '.$level->code : '' }}</p>
                        <h2 class="mt-1 text-lg font-extrabold text-ink">{{ $level->name ?? 'Общий уровень' }}</h2>
                        <p class="mt-1 text-sm text-ink/50">{{ $level->description ?? 'Упражнения без привязки к уровню.' }}</p>

                        <div class="mt-5 space-y-1 border-t border-line pt-4">
                            @foreach ($levelExercises as $exercise)
                                <a href="{{ route('exercises.show', $exercise->id) }}" class="group flex items-center gap-3 rounded-xl px-3 py-2.5 transition hover:bg-surface2">
                                    <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full border-2 border-line text-[10px] font-bold text-ink/40 transition group-hover:border-brand group-hover:text-brand">{{ $loop->iteration }}</span>
                                    <span class="flex-1 font-semibold text-ink transition group-hover:text-brand">{{ $exercise->title }}</span>
                                    <span class="text-xs font-bold text-ink/40">{{ $exercise->questions_count }} вопр.</span>
                                </a>
                            @endforeach
                        </div>
                    </x-ui.card>
                @endforeach
            </div>
        @endif
    </div>
@endsection
