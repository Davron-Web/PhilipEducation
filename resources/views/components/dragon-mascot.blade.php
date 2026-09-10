{{-- Маскот раздела Speaking: дракончик в наушниках вместо прежнего
     3D-робота. Чистый inline-SVG + CSS-анимация вместо Three.js — не тянет
     внешнюю библиотеку и не грузит WebGL ради статичной по сути картинки.

     Состояние (idle/listening/thinking/speaking) переключается атрибутом
     data-state на корневом элементе — вся анимация решается CSS-селекторами
     вида [data-state="listening"] .dm-…, JS только меняет атрибут
     (см. initDragon() в bot.blade.php).

     Палитра — только токены проекта: --pe-gold (кожа, обод и блик в
     глазах), --pe-navy/--pe-navy2 (глаза, чашки наушников, рот — не
     меняются между темами, поэтому не «плывут»), --pe-brand (тёплая тень
     на теле), --pe-sky (холодный акцент: кольца прослушивания и искра
     раздумья). Глаза сделаны тёмными (не золотыми, как кожа) сознательно —
     иначе радужка сливается с головой и не видна. --}}
@props(['id' => 'dragon-mascot'])

<div {{ $attributes->merge(['class' => 'dragon-mascot', 'id' => $id, 'data-state' => 'idle']) }}>
    <svg viewBox="0 0 220 220" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
        {{-- Свечение позади — то же место в композиции, что было у робота --}}
        <circle class="dm-glow" cx="110" cy="103" r="95" />

        <g class="dm-float">
            <g class="dm-tilt">
                {{-- Кольца прослушивания — расходятся от головы, видны только
                     в состоянии listening --}}
                <circle class="dm-pulse-ring" cx="110" cy="100" r="62" />
                <circle class="dm-pulse-ring dm-pulse-ring--2" cx="110" cy="100" r="62" />

                {{-- Рожки — эллипсы, а не путь с острым концом: гарантированно
                     без единого угла. Основание скрыто под головой (рисуется
                     раньше неё), наружу торчит только скруглённый кончик. --}}
                <ellipse class="dm-horn" cx="70" cy="46" rx="8" ry="17" transform="rotate(-24 70 46)" />
                <ellipse class="dm-horn" cx="150" cy="46" rx="8" ry="17" transform="rotate(24 150 46)" />

                {{-- Искра-мысль — только в состоянии thinking --}}
                <g class="dm-spark" transform="translate(168 40)">
                    <path d="M0 -9 L2.4 -2.4 9 0 2.4 2.4 0 9 -2.4 2.4 -9 0 -2.4 -2.4Z" />
                </g>

                {{-- Голова --}}
                <circle class="dm-skin" cx="110" cy="100" r="60" />
                {{-- Тёплая тень с левого края — придаёт объём одним пятном --}}
                <path class="dm-shade" d="M54 100a56 56 0 0 0 20 66 62 62 0 0 1-30-50 62 62 0 0 1 10-16Z" />

                {{-- Мордочка — чуть светлее и с тонкой обводкой, чтобы читалась
                     отдельно от головы, а не сливалась в один блин --}}
                <circle class="dm-snout" cx="110" cy="135" r="28" />

                {{-- Ноздри --}}
                <ellipse class="dm-navy-dot" cx="102" cy="140" rx="2.6" ry="3.4" />
                <ellipse class="dm-navy-dot" cx="118" cy="140" rx="2.6" ry="3.4" />

                {{-- Чашки наушников — рисуем раньше ленты, чтобы лента легла
                     поверх. Вынесены дальше от лица, чтобы не упираться в
                     глаза (в первой версии чашка и глаз почти соприкасались). --}}
                <circle class="dm-cup" cx="35" cy="96" r="22" />
                <circle class="dm-cup" cx="185" cy="96" r="22" />

                {{-- Дуга наушников: толще и ближе к голове — читается как
                     обод, а не тонкая линия над макушкой --}}
                <path class="dm-band" d="M38 78 Q110 30 182 78" />

                {{-- Глаза: тёмный «зрачок»-база + золотой блик + веко для
                     моргания. Тёмная база — единственный способ увидеть
                     глаз на голове того же золотого цвета. --}}
                <g class="dm-eye" transform="translate(86 95)">
                    <ellipse class="dm-eye-base" rx="14" ry="16" />
                    <circle class="dm-eye-shine" cx="-4" cy="-5" r="4.5" />
                    <rect class="dm-eyelid" x="-16" y="-18" width="32" height="36" />
                </g>
                <g class="dm-eye" transform="translate(134 95)">
                    <ellipse class="dm-eye-base" rx="14" ry="16" />
                    <circle class="dm-eye-shine" cx="4" cy="-5" r="4.5" />
                    <rect class="dm-eyelid" x="-16" y="-18" width="32" height="36" />
                </g>

                {{-- Рот: улыбка-дуга в покое, открытый овал — когда говорит --}}
                <path class="dm-mouth-smile" d="M96 150q14 13 28 0" />
                <ellipse class="dm-mouth-open" cx="110" cy="151" rx="10" ry="8" />
            </g>
        </g>
    </svg>
</div>

<style>
    .dragon-mascot { display: block; width: 100%; height: 100%; }
    /* overflow НЕ visible: с ним искра thinking-состояния однажды
       «сбежала» за пределы карточки — SVG с overflow:visible не режет
       содержимое по своей рамке в DOM-layout, только по viewBox. Все
       фигуры и так укладываются в 0..220, обрезка им не мешает. */
    .dragon-mascot svg { display: block; width: 100%; height: 100%; }

    .dragon-mascot .dm-glow { fill: var(--pe-gold); opacity: .16; }
    .dragon-mascot .dm-skin { fill: var(--pe-gold); }
    .dragon-mascot .dm-snout { fill: var(--pe-gold); stroke: var(--pe-navy2); stroke-width: 1.5; stroke-opacity: .3; }
    .dragon-mascot .dm-shade { fill: var(--pe-brand); opacity: .4; }
    .dragon-mascot .dm-horn { fill: var(--pe-gold); }
    .dragon-mascot .dm-navy-dot { fill: var(--pe-navy2); opacity: .55; }

    .dragon-mascot .dm-cup { fill: var(--pe-navy2); stroke: var(--pe-gold); stroke-width: 3; }
    .dragon-mascot .dm-band { stroke: var(--pe-gold); stroke-width: 11; stroke-linecap: round; }

    .dragon-mascot .dm-eye-base { fill: var(--pe-navy2); }
    .dragon-mascot .dm-eye-shine { fill: var(--pe-gold); }
    .dragon-mascot .dm-eyelid { fill: var(--pe-gold); transform: scaleY(0); transform-origin: center; }

    .dragon-mascot .dm-mouth-smile { stroke: var(--pe-navy2); stroke-width: 4.5; stroke-linecap: round; transition: opacity .15s ease; }
    .dragon-mascot .dm-mouth-open { fill: var(--pe-navy2); opacity: 0; transform-origin: 110px 151px; transition: opacity .15s ease; }

    .dragon-mascot .dm-pulse-ring { fill: none; stroke: var(--pe-sky); stroke-width: 2.5; opacity: 0; transform-origin: 110px 100px; }
    .dragon-mascot .dm-spark { fill: var(--pe-sky); opacity: 0; transform-origin: 168px 40px; }

    /* ---- Непрерывная лёгкая жизнь: покачивание и моргание ---- */
    .dragon-mascot .dm-float { animation: dm-bob 3.2s ease-in-out infinite; transform-origin: 110px 110px; }
    .dragon-mascot .dm-band { animation: dm-sway 4.5s ease-in-out infinite; transform-origin: 110px 80px; }
    .dragon-mascot .dm-eyelid { animation: dm-blink 5s ease-in-out infinite; }

    @keyframes dm-bob { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-5px); } }
    @keyframes dm-sway { 0%, 100% { transform: rotate(-2deg); } 50% { transform: rotate(2deg); } }
    @keyframes dm-blink { 0%, 90%, 100% { transform: scaleY(0); } 94% { transform: scaleY(1); } }

    /* ---- listening: звуковые кольца + яркие глаза ---- */
    .dragon-mascot[data-state="listening"] .dm-pulse-ring { animation: dm-pulse 1.6s ease-out infinite; }
    .dragon-mascot[data-state="listening"] .dm-pulse-ring--2 { animation-delay: .8s; }
    .dragon-mascot[data-state="listening"] .dm-eye-shine { animation: dm-glow 1s ease-in-out infinite; }
    @keyframes dm-pulse { 0% { opacity: .55; transform: scale(.82); } 100% { opacity: 0; transform: scale(1.4); } }
    @keyframes dm-glow { 0%, 100% { opacity: 1; } 50% { opacity: .45; } }

    /* ---- thinking: наклон головы + искра + прикрытые глаза ---- */
    .dragon-mascot[data-state="thinking"] .dm-tilt { animation: dm-tilt 2s ease-in-out infinite; transform-origin: 110px 105px; }
    .dragon-mascot[data-state="thinking"] .dm-spark { animation: dm-spark 1.3s ease-in-out infinite; }
    .dragon-mascot[data-state="thinking"] .dm-eye-base { opacity: .6; }
    @keyframes dm-tilt { 0%, 100% { transform: rotate(-5deg); } 50% { transform: rotate(5deg); } }
    @keyframes dm-spark { 0%, 100% { opacity: 0; transform: scale(.5); } 50% { opacity: 1; transform: scale(1); } }

    /* ---- speaking: рот открывается в такт ---- */
    .dragon-mascot[data-state="speaking"] .dm-mouth-smile { opacity: 0; }
    .dragon-mascot[data-state="speaking"] .dm-mouth-open { opacity: 1; animation: dm-talk .32s ease-in-out infinite alternate; }
    @keyframes dm-talk { 0% { transform: scaleY(.55); } 100% { transform: scaleY(1.15); } }

    @media (prefers-reduced-motion: reduce) {
        .dragon-mascot * { animation: none !important; }
    }
</style>
