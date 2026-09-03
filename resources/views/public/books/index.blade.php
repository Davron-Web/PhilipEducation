@extends('layouts.app')

@section('title', 'Books')
@section('page_title', 'Books')
@section('page_description', $books->count() . ' books to read and listen to')

@section('content')
    <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
        <div class="mb-8" data-reveal>
            <h1 class="text-3xl font-extrabold text-ink sm:text-4xl">Книги</h1>
            <p class="mt-1 text-ink/60">{{ $books->count() }} книг для чтения и прослушивания.</p>
        </div>

        @if ($books->isEmpty())
            <x-ui.card :hover="false" class="py-16 text-center">
                <p class="text-ink/50">Книги пока не опубликованы. Загляните позже!</p>
            </x-ui.card>
        @else
            <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($books as $book)
                    @php
                        $currentPage = $readsByBook[$book->id] ?? null;
                        $isStarted = $currentPage !== null;
                    @endphp
                    <div class="flex h-full flex-col overflow-hidden rounded-2xl border border-line bg-armor2 shadow-soft card-lift">
                        <x-book-cover :book="$book" class="h-44 w-full" />
                        <div class="flex flex-1 flex-col p-5">
                            <div class="mb-2 flex items-center gap-2">
                                <x-ui.badge variant="level" :level="$book->level" />
                                <span class="inline-flex items-center gap-1 text-sm text-ink/50">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20" /><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z" /></svg>
                                    {{ $book->pages_count }} стр.
                                </span>
                            </div>
                            <h3 class="text-base font-bold text-ink">{{ $book->title }}</h3>
                            <p class="mb-3 text-sm text-ink/50">{{ $book->author }}</p>

                            @if ($book->description)
                                <p class="mb-4 flex-1 text-sm text-ink/50">{{ Str::limit($book->description, 90) }}</p>
                            @else
                                <div class="flex-1"></div>
                            @endif

                            <x-ui.button :href="route('books.read', $book).($isStarted ? '?page='.$currentPage : '')" size="sm" class="mt-auto">
                                @if ($isStarted)
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polygon points="5 3 19 12 5 21 5 3" /></svg>
                                    Продолжить — стр. {{ $currentPage }}
                                @else
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20" /><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z" /></svg>
                                    Читать
                                @endif
                            </x-ui.button>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
@endsection
