{{--
    Общая обёртка для страниц авторизации: логотип, заголовок, подзаголовок,
    форма на карточке по центру экрана, ссылка снизу (slot footer).

    При первом заходе за сессию сова пролетает над экраном и «сбрасывает»
    карточку с формой — она падает на место с лёгким отскоком. Анимация
    включается только классом .owl-intro, который вешает скрипт внизу:
    без JS (и при повторном заходе) форма просто сразу на месте, а сова
    не показывается вовсе. Системная настройка «уменьшить анимацию»
    тоже отключает всё это.
--}}
@props([
    'title' => '',
    'subtitle' => null,
])

<div id="auth-stage" class="relative flex min-h-[calc(100vh-4rem)] items-center justify-center overflow-hidden px-4 py-16">

    {{-- Сова-курьер --}}
    <div class="auth-owl pointer-events-none absolute left-1/2 top-[12%] z-20" aria-hidden="true">
        <svg width="76" height="76" viewBox="0 0 64 64" fill="none">
            {{-- крылья --}}
            <path class="auth-owl-wing auth-owl-wing-l" d="M16 30 C 4 33, 2 46, 10 53 C 15 48, 17 38, 16 30 Z" fill="#2E2E4E" stroke="#C9A961" stroke-width="1.2" />
            <path class="auth-owl-wing auth-owl-wing-r" d="M48 30 C 60 33, 62 46, 54 53 C 49 48, 47 38, 48 30 Z" fill="#2E2E4E" stroke="#C9A961" stroke-width="1.2" />

            {{-- уши --}}
            <path d="M19 21 L15 9 L27 16 Z" fill="#1A1A2E" stroke="#C9A961" stroke-width="1.2" stroke-linejoin="round" />
            <path d="M45 21 L49 9 L37 16 Z" fill="#1A1A2E" stroke="#C9A961" stroke-width="1.2" stroke-linejoin="round" />

            {{-- туловище --}}
            <ellipse cx="32" cy="37" rx="17" ry="19" fill="#1A1A2E" stroke="#C9A961" stroke-width="1.4" />
            <ellipse cx="32" cy="42" rx="9.5" ry="12" fill="#252540" />

            {{-- глаза --}}
            <circle cx="25" cy="32" r="7" fill="#F5F0E8" />
            <circle cx="39" cy="32" r="7" fill="#F5F0E8" />
            <circle class="auth-owl-pupil" cx="25" cy="32" r="3.1" fill="#1A1A2E" />
            <circle class="auth-owl-pupil" cx="39" cy="32" r="3.1" fill="#1A1A2E" />

            {{-- клюв и лапы --}}
            <path d="M32 37 l-3.2 4.4 h6.4 Z" fill="#C9A961" />
            <path d="M27 55 v4 M32 56 v4 M37 55 v4" stroke="#C9A961" stroke-width="1.6" stroke-linecap="round" />
        </svg>
    </div>

    <div class="auth-card w-full max-w-md">
        <div class="mb-8 text-center">
            <a href="{{ url('/') }}" class="inline-flex h-14 w-14 items-center justify-center rounded bg-gold text-navy shadow-soft">
                <x-owl-mark :size="28" />
            </a>
            <h1 class="mt-4 font-display text-2xl font-semibold text-ink">{{ $title }}</h1>
            @if ($subtitle)
                <p class="mt-1 text-sm text-ink/60">{{ $subtitle }}</p>
            @endif
        </div>

        <x-ui.card :reveal="false" :hover="false">
            {{ $slot }}
        </x-ui.card>

        @isset($footer)
            <p class="mt-6 text-center text-sm text-ink/60">{{ $footer }}</p>
        @endisset
    </div>
</div>

<style>
    /* Без класса .owl-intro сова спрятана, а форма просто на месте —
       так страница остаётся рабочей без JS и при повторном заходе. */
    #auth-stage .auth-owl { display: none; }

    #auth-stage.owl-intro .auth-owl {
        display: block;
        opacity: 0;
        animation: auth-owl-fly 2s cubic-bezier(.4, .1, .3, 1) forwards;
    }

    #auth-stage.owl-intro .auth-card {
        animation: auth-card-drop 2s cubic-bezier(.3, .8, .4, 1) forwards;
    }

    /* Пролёт: слева из-за края экрана, зависание над карточкой, уход вправо. */
    @keyframes auth-owl-fly {
        0%   { transform: translate(-60vw, -18vh) rotate(14deg); opacity: 0; }
        12%  { opacity: 1; }
        40%  { transform: translate(-50%, 0) rotate(0deg); opacity: 1; }
        52%  { transform: translate(-50%, -10px) rotate(-2deg); opacity: 1; }
        88%  { opacity: 1; }
        100% { transform: translate(60vw, -22vh) rotate(-14deg); opacity: 0; }
    }

    /* Карточка «выпадает» из лап в момент зависания совы (~40%). */
    @keyframes auth-card-drop {
        0%, 40% { transform: translateY(-130px) scale(.94); opacity: 0; }
        58%     { transform: translateY(10px) scale(1); opacity: 1; }
        72%     { transform: translateY(-6px); }
        86%     { transform: translateY(2px); }
        100%    { transform: translateY(0); opacity: 1; }
    }

    #auth-stage.owl-intro .auth-owl-wing {
        transform-box: view-box;
        animation: auth-owl-flap .26s ease-in-out infinite alternate;
    }
    #auth-stage.owl-intro .auth-owl-wing-l { transform-origin: 16px 30px; }
    #auth-stage.owl-intro .auth-owl-wing-r { transform-origin: 48px 30px; }

    @keyframes auth-owl-flap {
        from { transform: rotate(0deg); }
        to   { transform: rotate(-26deg); }
    }
    #auth-stage.owl-intro .auth-owl-wing-r { animation-name: auth-owl-flap-r; }
    @keyframes auth-owl-flap-r {
        from { transform: rotate(0deg); }
        to   { transform: rotate(26deg); }
    }

    /* Моргание — маленькая деталь, пока сова летит. */
    #auth-stage.owl-intro .auth-owl-pupil {
        animation: auth-owl-blink 2s steps(1, end) 2;
    }
    @keyframes auth-owl-blink {
        0%, 44%, 52%, 100% { opacity: 1; }
        46%, 50%           { opacity: .15; }
    }

    @media (prefers-reduced-motion: reduce) {
        #auth-stage.owl-intro .auth-owl { display: none; }
        #auth-stage.owl-intro .auth-card,
        #auth-stage.owl-intro .auth-owl-wing,
        #auth-stage.owl-intro .auth-owl-pupil { animation: none; }
    }
</style>

<script>
(function () {
    var stage = document.getElementById('auth-stage');
    if (!stage) return;

    var KEY = 'pe-owl-intro-played';

    // Один раз за сессию браузера: после неудачного входа страница
    // перезагружается, и повторять анимацию было бы навязчиво.
    try {
        if (sessionStorage.getItem(KEY) === '1') return;
        sessionStorage.setItem(KEY, '1');
    } catch (e) {
        // Приватный режим — просто проигрываем каждый раз.
    }

    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;

    stage.classList.add('owl-intro');
})();
</script>
