@extends('layouts.app')

@section('title', 'Books')
@section('page_title', 'Books')
@section('page_description', $books->count() . ' books to read and listen to')

@section('content')
    @if($books->isEmpty())
        <div class="text-center py-5">
            <i class="bi bi-book display-4 text-secondary"></i>
            <p class="text-secondary mt-3 mb-0">No books published yet. Check back soon!</p>
        </div>
    @else
        <div class="row g-3">
            @foreach($books as $book)
                @php
                    $currentPage = $readsByBook[$book->id] ?? null;
                    $isStarted = $currentPage !== null;
                    $initials = mb_strtoupper(mb_substr($book->title, 0, 1));
                @endphp
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="card card-hover shadow-sm h-100 rounded-4 border-0">
                        @if($book->cover_image)
                            <img src="{{ $book->cover_image }}" alt="{{ $book->title }}" class="rounded-top-4" style="height:160px;object-fit:cover;">
                        @else
                            <div class="rounded-top-4 d-flex align-items-center justify-content-center" style="height:160px;background:linear-gradient(135deg,#2563EB,#60A5FA);">
                                <span class="display-5 fw-bold text-white">{{ $initials }}</span>
                            </div>
                        @endif
                        <div class="card-body p-4 d-flex flex-column">
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <x-level-badge :level="$book->level" />
                                <span class="text-secondary small"><i class="bi bi-journal-text me-1"></i>{{ $book->pages_count }} pages</span>
                            </div>
                            <h3 class="h6 fw-bold mb-1">{{ $book->title }}</h3>
                            <p class="text-secondary small mb-3">by {{ $book->author }}</p>

                            @if($book->description)
                                <p class="text-secondary small mb-3 flex-grow-1">{{ Str::limit($book->description, 90) }}</p>
                            @else
                                <div class="flex-grow-1"></div>
                            @endif

                            <a href="{{ route('books.read', $book) }}{{ $isStarted ? '?page=' . $currentPage : '' }}" class="btn btn-primary btn-sm mt-auto">
                                @if($isStarted)
                                    <i class="bi bi-play-fill me-1"></i>Continue — page {{ $currentPage }}
                                @else
                                    <i class="bi bi-book me-1"></i>Read
                                @endif
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
@endsection
