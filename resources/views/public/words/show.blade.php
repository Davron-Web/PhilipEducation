@extends('layouts.app')

@section('title', $word->word)
@section('page_title', 'Vocabulary')
@section('page_description', 'Word details')

@section('content')
    <div class="mb-3">
        <a href="{{ route('words.index') }}" class="link-secondary text-decoration-none small">
            <i class="bi bi-arrow-left me-1"></i>Back to vocabulary
        </a>
    </div>

    <div class="card shadow-sm rounded-4 border-0">
        <div class="card-body p-4">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div>
                    <h1 class="h4 fw-bold mb-1 text-capitalize">
                        {{ $word->word }}
                        <x-speak-button :word="$word->word" :audio-url="$word->audio_url" />
                    </h1>
                    @if($word->transcription)
                        <span class="text-secondary">/{{ $word->transcription }}/</span>
                    @endif
                </div>
                <span class="badge {{ $isLearned ? 'text-bg-success' : 'text-bg-secondary' }} rounded-pill">
                    {{ $isLearned ? 'Learned' : 'New' }}
                </span>
            </div>

            @if($word->translations->isNotEmpty())
                <h2 class="h6 fw-semibold mb-2">Translations</h2>
                <ul class="list-group list-group-flush mb-3">
                    @foreach($word->translations as $translation)
                        <li class="list-group-item px-0 d-flex justify-content-between">
                            <span>{{ $translation->translation }}</span>
                            <span class="text-secondary small text-uppercase">{{ $translation->language }}</span>
                        </li>
                        @if($translation->definition)
                            <li class="list-group-item px-0 border-0 pt-0 text-secondary small">{{ $translation->definition }}</li>
                        @endif
                    @endforeach
                </ul>
            @endif

            @if($word->example)
                <h2 class="h6 fw-semibold mb-2">Example</h2>
                <p class="fst-italic text-secondary">&ldquo;{{ $word->example }}&rdquo;</p>
            @endif

            @if($word->lesson)
                <a href="{{ route('lessons.show', $word->lesson->id) }}" class="btn btn-outline-primary mt-2">
                    <i class="bi bi-journal-bookmark me-1"></i>From lesson: {{ $word->lesson->title }}
                </a>
            @endif
        </div>
    </div>
@endsection
