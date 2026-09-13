@props([
    'levelCode',
    'levelName',
    'earned' => false,
    'recipientName' => null,
    'certificateNumber' => null,
    'issuedAt' => null,
])

{{--
    Компактное превью сертификата для карточки на /achievements —
    масштабированная копия канонического макета из
    resources/views/certificates/pdf.blade.php (один макет на оба места,
    см. комментарий там). Холст 1122×794 (A4 landscape) вписывается в
    ширину карточки через transform: scale(), коэффициент считает Alpine
    от реальной ширины обёртки. Полученный сертификат кликабелен —
    открывает тот же макет крупнее в модалке.
--}}
@php
    $displayName = $earned ? ($recipientName ?: 'Your Name') : 'Your Name';
    $displayNumber = $earned ? ($certificateNumber ?: '—') : 'CERT-XX-XXXX';
    $displayDateLabel = $earned
        ? ($issuedAt ? \Illuminate\Support\Carbon::parse($issuedAt)->translatedFormat('d M Y') : '—')
        : 'DD.MM.YYYY';
@endphp

@once
    <style>
        .cert-preview-frame {
            position: relative;
            width: 100%;
            aspect-ratio: 1122 / 794;
            overflow: hidden;
            background: #f3ecd8;
        }
        .cert-preview-canvas {
            position: absolute;
            top: 0;
            left: 0;
            width: 1122px;
            height: 794px;
            transform-origin: top left;
        }
        .cert-preview-overlay {
            position: absolute;
            inset: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 1.5rem;
            background: linear-gradient(180deg, rgba(11, 31, 58, .18), rgba(11, 31, 58, .58));
        }
        .earned-card .cert-preview-frame { transition: box-shadow .3s ease; }
        .earned-card:hover .cert-preview-frame { box-shadow: 0 0 34px 4px color-mix(in srgb, var(--pe-brand) 40%, transparent); }
    </style>
@endonce

<div
    x-data="{
        scale: .3,
        modalScale: 1,
        open: false,
        resize() { this.scale = this.$refs.frame.offsetWidth / 1122 },
        resizeModal() { this.modalScale = this.$refs.modalFrame ? this.$refs.modalFrame.offsetWidth / 1122 : 1 },
    }"
    x-init="resize(); window.addEventListener('resize', () => { resize(); if (open) resizeModal() })"
>
    <div
        x-ref="frame"
        class="cert-preview-frame rounded-t-2xl{{ $earned ? ' cursor-pointer' : '' }}"
        @if ($earned)
            x-on:click="open = true; setTimeout(() => resizeModal(), 50)"
        @endif
    >
        <div
            class="cert-preview-canvas"
            :style="`transform: scale(${scale})`"
            @unless ($earned) style="filter: grayscale(1); opacity: .32;" @endunless
        >
            @include('certificates.pdf', [
                'levelCode' => $levelCode,
                'levelName' => $levelName,
                'recipientName' => $displayName,
                'certificateNumber' => $displayNumber,
                'issuedAtLabel' => $displayDateLabel,
            ])
        </div>

        @unless ($earned)
            <div class="cert-preview-overlay">
                <span class="rounded-lg px-4 py-2 text-sm font-bold text-white" style="background-color: color-mix(in srgb, var(--pe-navy) 70%, transparent); backdrop-filter: blur(2px);">Complete the level to unlock</span>
            </div>
        @endunless
    </div>

    @if ($earned)
        {{-- x-teleport выносит модалку в конец <body>: карточки на этой
             странице анимируются через [data-reveal] (transform на время
             въезда), а любой transform на предке ломает position:fixed у
             потомка — модалка иначе позиционировалась бы от карточки,
             а не от вьюпорта. Teleport убирает эту зависимость насовсем. --}}
        <template x-teleport="body">
            <div
                x-show="open"
                style="display: none;"
                class="fixed inset-0 z-50 flex items-center justify-center p-4"
                x-on:click.self="open = false"
                x-on:keydown.escape.window="open = false"
            >
                <div class="absolute inset-0" style="background-color: color-mix(in srgb, var(--pe-navy) 70%, transparent); backdrop-filter: blur(4px);"></div>

                <div class="relative w-full" style="max-width: 1000px;">
                    <button
                        type="button"
                        x-on:click="open = false"
                        class="absolute -top-11 right-0 flex h-9 w-9 items-center justify-center rounded-full bg-armor2 text-ink shadow-lg"
                        aria-label="Закрыть"
                    >
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M6 6l12 12M6 18 18 6" /></svg>
                    </button>

                    <div
                        x-ref="modalFrame"
                        class="cert-preview-frame overflow-hidden rounded-2xl shadow-2xl"
                        style="aspect-ratio: 1122 / 794;"
                    >
                        <div class="cert-preview-canvas" :style="`transform: scale(${modalScale})`">
                            @include('certificates.pdf', [
                                'levelCode' => $levelCode,
                                'levelName' => $levelName,
                                'recipientName' => $displayName,
                                'certificateNumber' => $displayNumber,
                                'issuedAtLabel' => $displayDateLabel,
                            ])
                        </div>
                    </div>
                </div>
            </div>
        </template>
    @endif
</div>
