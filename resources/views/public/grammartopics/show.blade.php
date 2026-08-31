{{-- Страница темы грамматики: объяснение, примеры, практические задания.
     Абзацы вида "Важно: ..." подсвечиваются как предупреждение — это
     единственный устойчивый маркер "частой ошибки" в исходном тексте. --}}
@extends('layouts.app')

@section('title', $topic->title)
@section('page_title', 'Грамматика')
@section('meta_description', Str::limit(strip_tags($topic->theory), 150))

@section('content')
    <div class="mx-auto max-w-3xl px-4 py-10 sm:px-6 lg:px-8">
        <a href="{{ route('grammartopics.index') }}" class="mb-6 inline-flex items-center gap-1 text-sm font-semibold text-ink/60 transition hover:text-brand">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 12H5M11 18l-6-6 6-6" /></svg>
            Все темы грамматики
        </a>

        <x-ui.card :hover="false">
            <x-ui.badge variant="level" :level="$topic->level" class="mb-3" />
            <h1 class="text-2xl font-extrabold text-ink sm:text-3xl">{{ $topic->title }}</h1>

            @php
                $paragraphs = collect(preg_split('/\n{2,}/', trim($topic->theory)))->filter()->values();
            @endphp

            <div class="mt-6 space-y-4">
                @foreach ($paragraphs as $paragraph)
                    @php
                        $lines = explode("\n", $paragraph);
                        preg_match('/^([^:]{2,40}):\s*(.*)$/u', $lines[0], $m);
                        $label = $m ? Str::lower($m[1]) : '';

                        // Ярлык абзаца определяет визуальное оформление блока —
                        // единственный способ разметки без отдельных полей в БД.
                        $box = match (true) {
                            Str::contains($label, 'важно') => [
                                'wrap' => 'flex gap-3 rounded-2xl border border-sun/30 bg-sun/10 p-4',
                                'text' => 'text-sm leading-relaxed text-sun/90',
                                'label' => 'font-bold',
                                'icon' => '<path d="M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0Z" /><path d="M12 9v4M12 17h.01" />',
                                'iconClass' => 'text-sun',
                            ],
                            Str::contains($label, 'ошиб') => [
                                'wrap' => 'flex gap-3 rounded-2xl border border-red-400/30 bg-red-400/10 p-4',
                                'text' => 'text-sm leading-relaxed text-red-200',
                                'label' => 'font-bold',
                                'icon' => '<circle cx="12" cy="12" r="10" /><path d="M15 9l-6 6M9 9l6 6" />',
                                'iconClass' => 'text-red-300',
                            ],
                            Str::contains($label, 'пример') => [
                                'wrap' => 'flex gap-3 rounded-2xl border border-sky/30 bg-sky/10 p-4',
                                'text' => 'text-sm leading-relaxed text-sky-100',
                                'label' => 'font-bold text-sky',
                                'icon' => '<path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z" />',
                                'iconClass' => 'text-sky',
                            ],
                            Str::contains($label, 'структур') || Str::contains($label, 'формул') => [
                                'wrap' => 'flex gap-3 rounded-2xl border border-brand/30 bg-brand/10 p-4',
                                'text' => 'text-sm leading-relaxed text-ink/90',
                                'label' => 'font-bold text-brand',
                                'icon' => '<rect x="3" y="3" width="7" height="7" rx="1.5" /><rect x="14" y="3" width="7" height="7" rx="1.5" /><rect x="3" y="14" width="7" height="7" rx="1.5" /><rect x="14" y="14" width="7" height="7" rx="1.5" />',
                                'iconClass' => 'text-brand',
                            ],
                            Str::contains($label, 'использ') => [
                                'wrap' => 'flex gap-3 rounded-2xl border border-skylight/30 bg-skylight/10 p-4',
                                'text' => 'text-sm leading-relaxed text-skylight',
                                'label' => 'font-bold',
                                'icon' => '<circle cx="12" cy="12" r="10" /><path d="M12 16v-4M12 8h.01" />',
                                'iconClass' => 'text-skylight',
                            ],
                            default => null,
                        };
                    @endphp

                    @if ($box)
                        <div class="{{ $box['wrap'] }}">
                            <span class="mt-0.5 shrink-0 {{ $box['iconClass'] }}" aria-hidden="true">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">{!! $box['icon'] !!}</svg>
                            </span>
                            <p class="{{ $box['text'] }}">
                                <strong class="{{ $box['label'] }}">{{ $m[1] }}:</strong> {{ $m[2] }}
                                @foreach (array_slice($lines, 1) as $line)
                                    <br>{{ $line }}
                                @endforeach
                            </p>
                        </div>
                    @else
                        <p class="leading-relaxed text-ink/80">
                            @if ($m)
                                <strong class="font-bold text-ink">{{ $m[1] }}:</strong> {{ $m[2] }}
                            @else
                                {{ $lines[0] }}
                            @endif
                            @foreach (array_slice($lines, 1) as $line)
                                <br>{{ $line }}
                            @endforeach
                        </p>
                    @endif
                @endforeach
            </div>
        </x-ui.card>

        @php $exercises = $topic->lessons->flatMap->exercises; @endphp
        @if ($exercises->isNotEmpty())
            <x-ui.card :hover="false" class="mt-6">
                <h2 class="mb-4 flex items-center gap-2 text-lg font-bold text-ink">
                    <svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4Z" /></svg>
                    Практика по теме
                </h2>
                <div class="space-y-2">
                    @foreach ($exercises as $exercise)
                        <a
                            href="{{ route('exercises.show', $exercise->id) }}"
                            class="flex items-center justify-between rounded-xl px-3 py-2.5 text-sm font-semibold text-ink/80 transition hover:bg-brand/5 hover:text-brand"
                        >
                            {{ $exercise->title }}
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M13 6l6 6-6 6" /></svg>
                        </a>
                    @endforeach
                </div>
            </x-ui.card>
        @endif
    </div>
@endsection
