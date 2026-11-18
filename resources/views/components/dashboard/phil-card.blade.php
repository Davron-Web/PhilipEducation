{{-- Быстрый переход в чат с Phil — тем же способом, что и другие страницы
     сайта (window.openPhilWidget(), см. x-phil-widget в layouts/app.blade.php,
     подключён глобально). Виджет всегда доступен в правом нижнем углу —
     эта карточка просто открывает его сразу, без похода к иконке. --}}
@props(['delay' => 0])

<x-ui.card class="!p-6" :hover="true" style="transition-delay: {{ $delay }}ms">
    <div class="mb-3 flex items-center gap-3">
        <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-navy text-gold">
            <x-mascot :size="28" />
        </span>
        <div>
            <h3 class="text-lg font-bold text-ink">Спросите Phil</h3>
            <p class="text-xs text-ink/50">ИИ-ассистент, онлайн всегда</p>
        </div>
    </div>

    <p class="text-sm text-ink/60">
        Не поняли грамматику, слово или произношение? Phil объяснит с примерами
        прямо в чате — не нужно искать ответ самому.
    </p>

    <x-ui.button
        onclick="window.openPhilWidget && window.openPhilWidget()"
        class="mt-5 w-full"
    >
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z" /></svg>
        Начать чат
    </x-ui.button>
</x-ui.card>
