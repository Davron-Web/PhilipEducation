{{-- Маскот раздела Speaking: жёлтый смайлик Фил, без национальной
     символики — предыдущая версия рисовала его на фоне флага Таджикистана,
     в таджикской тюбетейке с надписью «Тоҷикистон», это убрано целиком по
     прямому указанию (не архивировано, как и версия-лев до неё).

     Смайлик остался ровно тем же кругом с глазами и улыбкой, что и раньше —
     менялась только национальная символика вокруг него, а не сам
     персонаж. Фон отдельно не рисуем: SVG прозрачный, смайлик сидит прямо
     на фоне своей карточки (#robo-stage в bot.blade.php — тёмно-синий),
     как и просили — «фон цвета карточки», а не новый нарисованный диск.

     Состояние (idle/listening/thinking/speaking) — прежний контракт:
     атрибут data-state на корневом элементе, JS только его переключает
     (см. initPhilMascot() в bot.blade.php), вся анимация решается
     CSS-селекторами [data-state="…"] .pm-… — это НЕ трогали, у смайлика
     по-прежнему разные состояния (моргание, пульс при прослушивании,
     наклон при «размышлении», открывающийся рот при ответе). --}}
@props(['id' => 'phil-mascot'])

<div {{ $attributes->merge(['class' => 'phil-mascot', 'id' => $id, 'data-state' => 'idle']) }}>
    <svg viewBox="0 0 220 220" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
        {{-- Мягкое свечение позади всего --}}
        <circle class="pm-glow" cx="110" cy="110" r="95" />

        <g class="pm-float">
            <g class="pm-tilt">
                {{-- Кольца прослушивания --}}
                <circle class="pm-pulse-ring" cx="110" cy="118" r="66" />
                <circle class="pm-pulse-ring pm-pulse-ring--2" cx="110" cy="118" r="66" />

                {{-- Тень-подложка: отделяет жёлтый круг от тёмного фона сцены --}}
                <circle class="pm-shadow" cx="114" cy="124" r="57" />

                {{-- Смайлик: один круг, обводка, глаза, улыбка — без гривы,
                     ушей и прочих деталей --}}
                <circle class="pm-face" cx="110" cy="118" r="57" />

                <circle class="pm-eye" cx="93" cy="111" r="9" />
                <circle class="pm-eye" cx="127" cy="111" r="9" />

                <path class="pm-mouth-smile" d="M92 132q18 20 36 0" />
                <ellipse class="pm-mouth-open" cx="110" cy="136" rx="10" ry="8" />
            </g>
        </g>
    </svg>
</div>

<style>
    .phil-mascot { display: block; width: 100%; height: 100%; }
    .phil-mascot svg { display: block; width: 100%; height: 100%; }

    .phil-mascot .pm-glow { fill: var(--pe-gold); opacity: .16; }
    .phil-mascot .pm-shadow { fill: var(--pe-navy2); opacity: .3; }

    /* ---- Смайлик ---- */
    .phil-mascot .pm-face { fill: #FFCC00; stroke: var(--pe-navy2); stroke-width: 4; }
    .phil-mascot .pm-eye { fill: var(--pe-navy2); transform-origin: center; transition: transform .1s ease; }
    .phil-mascot .pm-mouth-smile { stroke: var(--pe-navy2); stroke-width: 5; stroke-linecap: round; transition: opacity .15s ease; }
    .phil-mascot .pm-mouth-open { fill: var(--pe-navy2); opacity: 0; transform-origin: 110px 136px; transition: opacity .15s ease; }

    .phil-mascot .pm-pulse-ring { fill: none; stroke: var(--pe-sky); stroke-width: 2.5; opacity: 0; transform-origin: 110px 118px; }

    /* ---- Непрерывная лёгкая жизнь: покачивание и моргание ---- */
    .phil-mascot .pm-float { animation: pm-bob 3.2s ease-in-out infinite; transform-origin: 110px 118px; }
    .phil-mascot .pm-eye { animation: pm-blink 5s ease-in-out infinite; }

    @keyframes pm-bob { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-5px); } }
    @keyframes pm-blink { 0%, 90%, 100% { transform: scaleY(1); } 94% { transform: scaleY(.1); } }

    /* ---- listening: звуковые кольца ---- */
    .phil-mascot[data-state="listening"] .pm-pulse-ring { animation: pm-pulse 1.6s ease-out infinite; }
    .phil-mascot[data-state="listening"] .pm-pulse-ring--2 { animation-delay: .8s; }
    @keyframes pm-pulse { 0% { opacity: .55; transform: scale(.82); } 100% { opacity: 0; transform: scale(1.4); } }

    /* ---- thinking: лёгкий наклон головы ---- */
    .phil-mascot[data-state="thinking"] .pm-tilt { animation: pm-tilt 2s ease-in-out infinite; transform-origin: 110px 118px; }
    @keyframes pm-tilt { 0%, 100% { transform: rotate(-5deg); } 50% { transform: rotate(5deg); } }

    /* ---- speaking: рот открывается в такт ---- */
    .phil-mascot[data-state="speaking"] .pm-mouth-smile { opacity: 0; }
    .phil-mascot[data-state="speaking"] .pm-mouth-open { opacity: 1; animation: pm-talk .32s ease-in-out infinite alternate; }
    @keyframes pm-talk { 0% { transform: scaleY(.55); } 100% { transform: scaleY(1.15); } }

    @media (prefers-reduced-motion: reduce) {
        .phil-mascot * { animation: none !important; }
    }
</style>
