{{--
    Общий чат как выезжающая слева панель (Clash of Clans-style), а не
    отдельная страница — подключена глобально в layouts/app.blade.php,
    доступна на любой странице сайта. Сама лента сообщений — x-chat.feed
    (та же разметка, что и на standalone-странице /chat, которая осталась
    рабочей по прямой ссылке).

    Состояние (открыта/закрыта) — в localStorage (не Alpine.$persist —
    его в проекте нигде нет, чтобы не тащить ещё один плагин ради одного
    boolean), поэтому не сбрасывается при переходе между страницами.

    z-index: 75 (подложка) / 80 (панель) / 81 (вкладка) — заметно ниже
    #phil-widget (99990, см. phil-widget.blade.php), чтобы кнопка Фила
    всегда оставалась поверх, даже когда панель открыта на всю ширину
    экрана на мобильных.
--}}
<div
    x-data="{
        open: localStorage.getItem('pe-chat-drawer-open') === '1',
        toggle() {
            this.open = !this.open;
            localStorage.setItem('pe-chat-drawer-open', this.open ? '1' : '0');
        },
        close() {
            if (this.open) {
                this.open = false;
                localStorage.setItem('pe-chat-drawer-open', '0');
            }
        },
    }"
    @keydown.escape.window="close()"
>
    {{-- Подложка — только для клика "вне панели"; на мобильных панель на
         всю ширину экрана и подложку не видно, но клик по ней (за пределами
         видимой области) всё равно не помешает — просто не понадобится. --}}
    <div
        x-show="open"
        x-transition:enter="transition-opacity duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition-opacity duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        @click="close()"
        class="fixed inset-0 z-[75] bg-ink/40"
        style="display: none"
        aria-hidden="true"
    ></div>

    <div
        class="fixed inset-y-0 left-0 z-[80] flex h-full w-full flex-col border-r border-line bg-armor2 shadow-softLg transition-transform duration-[250ms] ease-in-out sm:w-[360px]"
        :class="open ? 'translate-x-0' : '-translate-x-full'"
        role="dialog"
        aria-modal="true"
        aria-label="Общий чат"
    >
        <div class="flex shrink-0 items-center justify-between border-b border-line px-4 py-3">
            <h2 class="font-display text-sm font-bold uppercase tracking-wide text-ink/70">Общий чат</h2>
            <button
                type="button"
                @click="close()"
                class="flex h-8 w-8 items-center justify-center rounded-full text-ink/40 transition hover:bg-surface2 hover:text-ink"
                aria-label="Закрыть чат"
            >
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"><path d="M18 6 6 18M6 6l12 12" /></svg>
            </button>
        </div>

        <x-chat.feed :lazy="true" />
    </div>

    {{-- Вкладка: на десктопе "приклеена" к правому краю панели (сдвигается
         вместе с ней через тот же sm:translate-x-[360px]); на мобильных
         панель на всю ширину экрана, поэтому вкладка нарочно НЕ уезжает
         вместе с ней — остаётся у левого края поверх панели (z-81 > z-80),
         иначе на телефоне она укатилась бы за пределы экрана. --}}
    <button
        type="button"
        @click="toggle()"
        class="fixed left-0 top-1/2 z-[81] flex h-14 w-11 -translate-y-1/2 items-center justify-center rounded-r-2xl border border-l-0 border-line shadow-soft transition-[transform,background-color,color] duration-[250ms] ease-in-out"
        :class="open
            ? 'sm:translate-x-[360px] bg-brand text-white border-brand'
            : 'bg-armor2 text-ink/60 hover:text-ink'"
        :aria-expanded="open ? 'true' : 'false'"
        aria-label="Общий чат"
    >
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M7.9 20A9 9 0 1 0 4 16.1L2 22Z" /></svg>
    </button>
</div>
