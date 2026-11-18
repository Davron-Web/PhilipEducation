{{-- Итог теста на определение уровня. --}}
@extends('layouts.app')

@section('title', 'Ваш уровень английского')
@section('page_title', 'Placement Test')

@section('content')
    <div class="mx-auto max-w-2xl px-4 py-10 sm:px-6 lg:px-8">
        <x-ui.card :hover="false" class="text-center">
            <p class="text-sm font-bold uppercase tracking-wide text-ink/50">Ваш уровень</p>
            <p class="mt-2 font-display text-6xl font-bold text-brand">{{ $attempt->resultLevel->code }}</p>
            <p class="mt-1 text-lg font-semibold text-ink">{{ $attempt->resultLevel->name }}</p>
            @if ($practicalDescription)
                <p class="mx-auto mt-3 max-w-md text-sm text-ink/60">{{ $practicalDescription }}</p>
            @endif

            <p class="mx-auto mt-4 max-w-md text-xs text-ink/40">
                Этот уровень сохранён в вашем профиле — уроки и рекомендации
                теперь подбираются под него.
            </p>
        </x-ui.card>

        <x-ui.card :hover="false" class="mt-6">
            <h2 class="mb-4 text-sm font-bold uppercase tracking-wide text-ink/50">Разбор по уровням</h2>
            <div class="space-y-3">
                @foreach ($breakdown as $row)
                    <div class="flex items-center gap-3">
                        <span class="w-9 shrink-0 text-sm font-bold text-ink/70">{{ $row['code'] }}</span>
                        <div class="h-2.5 flex-1 overflow-hidden rounded-full bg-surface2">
                            <div
                                class="h-full rounded-full {{ $row['passed'] ? 'bg-brand' : 'bg-ink/20' }}"
                                style="width: {{ $row['total'] > 0 ? round($row['correct'] / $row['total'] * 100) : 0 }}%"
                            ></div>
                        </div>
                        <span class="w-14 shrink-0 text-right text-sm text-ink/50">{{ $row['correct'] }}/{{ $row['total'] }}</span>
                        @if ($row['passed'])
                            <svg class="shrink-0 text-brand" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 6 9 17l-5-5" /></svg>
                        @else
                            <svg class="shrink-0 text-ink/20" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M18 6 6 18M6 6l12 12" /></svg>
                        @endif
                    </div>
                @endforeach
            </div>
            <p class="mt-4 text-xs text-ink/40">
                Итоговый уровень — самый высокий, на котором верно решено
                не меньше 60% вопросов, при условии, что все более простые
                уровни тоже пройдены. Случайные верные ответы на уровнях
                выше вашего на результат не влияют.
            </p>
        </x-ui.card>

        <div class="mt-6 flex flex-wrap items-center justify-center gap-3">
            {{-- ?level= подхватывается фильтром на странице уроков (Alpine
                 читает его при загрузке) — реальная фильтрация, а не
                 просто ссылка на общий каталог. --}}
            <x-ui.button href="{{ route('lessons.index', ['level' => $attempt->resultLevel->code]) }}">
                Начать с уроков уровня {{ $attempt->resultLevel->code }}
            </x-ui.button>
            <form method="POST" action="{{ route('tests.placement.start') }}">
                @csrf
                <x-ui.button type="submit" variant="outline">Пройти ещё раз</x-ui.button>
            </form>
        </div>
    </div>
@endsection
