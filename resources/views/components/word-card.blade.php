@props(['word', 'learned' => false])

<div class="card card-hover shadow-sm h-100 rounded-4 border-0">
    @if($word->image)
        <img src="{{ asset($word->image) }}" alt="{{ $word->word }}" class="card-img-top rounded-top-4" style="height: 140px; object-fit: cover;">
    @endif
    <div class="card-body p-4 d-flex flex-column">
        <div class="d-flex align-items-start justify-content-between mb-2">
            <div>
                <h3 class="h6 fw-bold mb-0 text-capitalize">
                    {{ $word->word }}
                    <x-speak-button :word="$word->word" :audio-url="$word->audio_url" />
                </h3>
                @if($word->transcription)
                    <span class="text-secondary small">/{{ $word->transcription }}/</span>
                @endif
            </div>
            <span class="badge {{ $learned ? 'text-bg-success' : 'text-bg-secondary' }} rounded-pill">
                {{ $learned ? 'Learned' : 'New' }}
            </span>
        </div>

        @if($word->translations->isNotEmpty())
            <p class="fw-medium mb-2">{{ $word->translations->pluck('translation')->join(', ') }}</p>
        @endif

        @if($word->example)
            <p class="text-secondary small fst-italic mb-3 flex-grow-1">&ldquo;{{ $word->example }}&rdquo;</p>
        @endif

        <a href="{{ route('words.show', $word->id) }}" class="btn btn-outline-primary btn-sm mt-auto">
            View word <i class="bi bi-arrow-right ms-1"></i>
        </a>
    </div>
</div>
