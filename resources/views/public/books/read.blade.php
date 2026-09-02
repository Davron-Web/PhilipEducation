@extends('layouts.app')

@section('title', $book->title)
@section('page_title', 'Books')
@section('page_description', $book->title . ' — page ' . $page . ' of ' . $totalPages)

@section('content')
    <div class="mx-auto max-w-3xl px-4 py-10 sm:px-6 lg:px-8">
        <a href="{{ route('books.index') }}" class="mb-4 inline-flex items-center gap-1 text-sm font-semibold text-ink/60 transition hover:text-brand">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 12H5M11 18l-6-6 6-6" /></svg>
            Все книги
        </a>

        <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
            <div>
                <h1 class="text-xl font-extrabold text-ink">{{ $book->title }}</h1>
                <p class="mt-0.5 flex flex-wrap items-center gap-2 text-sm text-ink/50">
                    {{ $book->author }}
                    <x-ui.badge variant="level" :level="$book->level" />
                </p>
            </div>
            <div class="flex items-center gap-1.5">
                <button type="button" id="fontDecrease" class="reader-chip" title="Меньше текст">A-</button>
                <button type="button" id="fontIncrease" class="reader-chip" title="Больше текст">A+</button>
            </div>
        </div>

        <div class="mb-6 h-1.5 overflow-hidden rounded-full bg-surface2">
            <div class="h-full rounded-full bg-gradient-to-r from-brand to-sky" style="width: {{ round($page / max(1, $totalPages) * 100) }}%"></div>
        </div>

        {{-- Voice control panel --}}
        <x-ui.card :hover="false" class="mb-4 flex flex-wrap items-center gap-5">
            <div class="flex items-center gap-1.5">
                <button type="button" id="playBtn" class="reader-btn reader-btn-primary">Слушать</button>
                <button type="button" id="pauseBtn" class="reader-btn" disabled>Пауза</button>
                <button type="button" id="stopBtn" class="reader-btn" disabled>Стоп</button>
            </div>

            <div class="flex items-center gap-1.5">
                <span class="text-xs font-semibold text-ink/40">Скорость:</span>
                <button type="button" class="speed-btn reader-chip" data-rate="0.75">0.75x</button>
                <button type="button" class="speed-btn reader-chip active" data-rate="0.9">0.9x</button>
                <button type="button" class="speed-btn reader-chip" data-rate="1.0">1.0x</button>
            </div>

            <div class="flex items-center gap-1.5">
                <span class="text-xs font-semibold text-ink/40">Режим:</span>
                <button type="button" class="mode-btn reader-chip active" data-mode="continuous">Подряд</button>
                <button type="button" class="mode-btn reader-chip" data-mode="sentence">По предложениям</button>
            </div>
        </x-ui.card>

        {{-- Practice UI, shown only in sentence mode after each sentence --}}
        <div id="practiceBar" class="hidden mb-4 flex-wrap items-center justify-between gap-2 rounded-2xl border border-brand/30 bg-brand/5 px-4 py-3" role="status">
            <span class="font-semibold text-ink">Ваша очередь 👇 — повторите предложение вслух</span>
            <div class="flex gap-2">
                <button type="button" id="repeatBtn" class="reader-chip">🔁 Повторить</button>
                <button type="button" id="nextSentenceBtn" class="reader-btn reader-btn-primary">▶ Дальше</button>
            </div>
        </div>

        <x-ui.card :hover="false" class="mb-6">
            @if ($currentPage->title)
                <h2 class="mb-3 text-lg font-bold text-ink">{{ $currentPage->title }}</h2>
            @endif

            <div id="readerContent" class="reader-content text-ink/80">
                @foreach (preg_split('/\n\s*\n/', trim($currentPage->content ?? '')) as $paragraph)
                    @php $paragraph = trim($paragraph); @endphp
                    @if ($paragraph !== '')
                        <p class="reader-paragraph">{{ $paragraph }}</p>
                    @endif
                @endforeach
            </div>
        </x-ui.card>

        <div class="flex items-center justify-between">
            @if ($page > 1)
                <x-ui.button :href="route('books.read', $book).'?page='.($page - 1)" variant="outline" size="sm">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 12H5M11 18l-6-6 6-6" /></svg>
                    Назад
                </x-ui.button>
            @else
                <span></span>
            @endif

            <span class="text-sm text-ink/50">Стр. {{ $page }} из {{ $totalPages }}</span>

            @if ($page < $totalPages)
                <x-ui.button :href="route('books.read', $book).'?page='.($page + 1)" size="sm">
                    Далее
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M13 6l6 6-6 6" /></svg>
                </x-ui.button>
            @else
                <x-ui.badge variant="success">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 6 9 17l-5-5" /></svg>
                    Последняя страница
                </x-ui.badge>
            @endif
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .reader-content { font-size: 1.05rem; line-height: 1.9; transition: font-size .15s ease; }
        .reader-paragraph { margin-bottom: 1.1em; }
        .reader-content .w {
            cursor: pointer;
            border-radius: 4px;
            padding: 1px 2px;
            margin: -1px -2px;
            transition: background-color .15s ease, color .15s ease;
        }
        .reader-content .w:hover { background: color-mix(in srgb, var(--pe-brand) 15%, transparent); }
        .reader-content .w.speaking {
            background: var(--pe-brand);
            color: #fff;
            box-shadow: 0 0 0 2px color-mix(in srgb, var(--pe-brand) 35%, transparent);
        }

        .reader-btn {
            border-radius: .65rem;
            border: 1px solid var(--pe-line);
            padding: .45rem .9rem;
            font-size: .8rem;
            font-weight: 700;
            color: var(--pe-ink);
            background: var(--pe-armor2);
            transition: .2s ease;
        }
        .reader-btn:hover:not(:disabled) { border-color: var(--pe-brand); color: var(--pe-brand); }
        .reader-btn:disabled { opacity: .4; cursor: not-allowed; }
        .reader-btn-primary { background: var(--pe-brand); border-color: var(--pe-brand); color: #fff; }
        .reader-btn-primary:hover:not(:disabled) { color: #fff; opacity: .9; }

        .reader-chip {
            border-radius: 999px;
            border: 1px solid var(--pe-line);
            padding: .3rem .75rem;
            font-size: .75rem;
            font-weight: 700;
            color: var(--pe-ink);
            background: var(--pe-armor2);
            transition: .2s ease;
        }
        .reader-chip:hover { border-color: var(--pe-brand); }
        .reader-chip.active { background: var(--pe-brand); border-color: var(--pe-brand); color: #fff; }
    </style>
@endpush

@push('scripts')
<script src="{{ asset('assets/js/pronounce.js') }}"></script>
<script src="{{ asset('assets/js/reader.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        initReader({
            saveUrl: {{ Js::from(route('books.progress', $book)) }},
            page: {{ (int) $page }},
        });
    });
</script>
@endpush
