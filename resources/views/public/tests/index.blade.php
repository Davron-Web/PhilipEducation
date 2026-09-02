@extends('layouts.app')

@section('title', 'Tests')
@section('page_title', 'Tests')
@section('page_description', 'Check your knowledge by level')

@section('content')
    <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
        <div class="mb-8" data-reveal>
            <h1 class="text-3xl font-extrabold text-ink sm:text-4xl">Тесты</h1>
            <p class="mt-1 text-ink/60">Проверьте свои знания по уровням.</p>
        </div>

        @if ($testsByLevel->isEmpty())
            <x-ui.card :hover="false" class="py-16 text-center">
                <p class="text-ink/50">Тесты пока не опубликованы. Загляните позже!</p>
            </x-ui.card>
        @else
            @foreach ($testsByLevel as $level => $tests)
                <div class="mb-3 flex items-center gap-2" data-reveal>
                    <h2 class="text-lg font-extrabold text-ink">{{ $level }}</h2>
                    <span class="text-sm text-ink/50">{{ $tests->count() }} тестов</span>
                </div>
                <div class="mb-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                    @foreach ($tests as $test)
                        <x-test-card :test="$test" />
                    @endforeach
                </div>
            @endforeach
        @endif
    </div>
@endsection
