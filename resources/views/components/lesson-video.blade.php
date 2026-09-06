{{-- Один видеоурок: плеер разворачивается по клику на обложку.

     Iframe не грузится сразу: три-четыре встроенных плеера на странице
     тянут по мегабайту скриптов каждый и заметно тормозят открытие урока.
     До клика показываем картинку, после — сам плеер. --}}
@props(['video'])

@php $embed = $video->embedUrl(); @endphp

<div
    class="overflow-hidden rounded-2xl border border-line bg-armor2 shadow-soft"
    x-data="{ playing: false }"
>
    <div class="relative aspect-video bg-navy">
        @if ($embed)
            <template x-if="playing">
                <iframe
                    src="{{ $embed }}&autoplay=1"
                    title="{{ $video->title }}"
                    class="absolute inset-0 h-full w-full"
                    frameborder="0"
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                    allowfullscreen
                ></iframe>
            </template>

            <button
                type="button"
                x-show="!playing"
                @click="playing = true"
                class="group absolute inset-0 flex items-center justify-center"
                aria-label="Смотреть: {{ $video->title }}"
            >
                @if ($poster = $video->thumbnailUrl())
                    <img src="{{ $poster }}" alt="" class="absolute inset-0 h-full w-full object-cover opacity-70 transition group-hover:opacity-90">
                @endif

                <span class="relative flex h-16 w-16 items-center justify-center rounded-full bg-gold text-navy shadow-lg transition group-hover:scale-110">
                    <svg width="26" height="26" viewBox="0 0 24 24" fill="currentColor"><path d="M8 5v14l11-7z" /></svg>
                </span>
            </button>
        @else
            {{-- Ссылку не удалось разобрать — плеер не показываем, но и урок
                 не ломаем: даём открыть видео на исходном сайте. --}}
            <a href="{{ $video->url }}" target="_blank" rel="noopener noreferrer"
               class="absolute inset-0 flex flex-col items-center justify-center gap-2 text-white/70 transition hover:text-gold">
                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="m22 8-6 4 6 4V8Z" /><rect x="2" y="6" width="14" height="12" rx="2" /></svg>
                <span class="text-xs font-bold uppercase tracking-wider">Открыть видео</span>
            </a>
        @endif
    </div>

    <div class="p-4">
        <h3 class="font-bold text-ink">{{ $video->title }}</h3>

        @if ($video->description)
            <p class="mt-1 text-sm text-ink/60">{{ $video->description }}</p>
        @endif

        @if ($video->duration_minutes)
            <p class="mt-2 inline-flex items-center gap-1.5 text-xs font-semibold text-ink/50">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9" /><path d="M12 7v5l3 2" /></svg>
                {{ $video->duration_minutes }} мин
            </p>
        @endif
    </div>
</div>
