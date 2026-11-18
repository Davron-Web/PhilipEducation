{{-- Разговорная практика с Филом: жёлтый смайлик в тюбетейке (тот же
     персонаж, что и AI-ассистент Phil в виджете внизу справа),
     распознавание речи, ответ голосом и разбор ошибок после разговора.

     Маскот — inline-SVG + CSS-анимация (components/phil-mascot.blade.php),
     без внешних библиотек. Распознавание речи — Web Speech API, он есть
     в Chrome/Edge; в других браузерах показываем честное предупреждение
     вместо неработающей кнопки. --}}
@extends('layouts.app')

@section('title', __('site.speaking.page_title'))
@section('page_title', 'Speaking')

@push('styles')
    <style>
        #robo-stage { height: 320px; overflow: hidden; padding: 12px; box-sizing: border-box; }
        .robo-bubble { max-width: 85%; }
    </style>
@endpush

@section('content')
{{-- x-init не нужен: Alpine сам вызывает init() у объекта данных.
     С обоими робот собирался дважды — две сцены поверх друг друга. --}}
<div class="mx-auto max-w-4xl px-4 py-10 sm:px-6 lg:px-8" x-data="speakingBot()">

    <a href="{{ route('ielts.speaking.index') }}" class="mb-4 inline-flex items-center gap-1 text-sm font-semibold text-ink/60 transition hover:text-brand">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 12H5M11 18l-6-6 6-6" /></svg>
        Speaking
    </a>

    <div class="mb-6">
        <h1 class="font-display text-3xl font-semibold text-ink">{{ __('site.speaking.page_title') }}</h1>
        <p class="mt-1 text-ink/60">Говорите вслух по-английски — {{ __('site.speaking.mascot_name') }} слушает, отвечает и в конце разберёт ошибки.</p>
    </div>

    {{-- Браузер не поддерживает распознавание --}}
    <template x-if="!supported">
        <x-ui.card :hover="false" class="mb-6 border-sun/40">
            <p class="font-bold text-ink">Этот браузер не распознаёт речь</p>
            <p class="mt-2 text-sm text-ink/60">
                Голосовой ввод работает через Web Speech API — он есть в Chrome и Edge.
                Откройте страницу в одном из них либо пишите ответы текстом ниже.
            </p>
        </x-ui.card>
    </template>

    {{-- Маскот --}}
    <div class="overflow-hidden rounded-2xl border border-line bg-navy shadow-soft">
        <div id="robo-stage"><x-phil-mascot id="phil-mascot" /></div>
        <div class="flex flex-wrap items-center justify-between gap-3 border-t border-white/10 px-5 py-3">
            <p class="text-sm font-semibold text-white/80" x-text="statusText"></p>
            <div class="flex items-center gap-2">
                <label class="flex items-center gap-2 text-xs font-semibold text-white/60">
                    Уровень
                    <select x-model="level" class="rounded border border-white/15 bg-navy2 px-2 py-1 text-xs text-white">
                        <option>A1</option><option>A2</option>
                        <option selected>B1</option><option>B2</option>
                        <option>C1</option>
                    </select>
                </label>
            </div>
        </div>
    </div>

    {{-- Диалог --}}
    <div class="mt-6 space-y-3" x-ref="log">
        <template x-for="(turn, i) in history" :key="i">
            <div :class="turn.role === 'user' ? 'flex justify-end' : 'flex justify-start'">
                <div
                    class="robo-bubble rounded-xl px-4 py-2.5 text-sm leading-relaxed shadow-sm"
                    :class="turn.role === 'user' ? 'bg-brand text-white' : 'border border-line bg-armor2 text-ink'"
                >
                    <span x-text="turn.text"></span>
                </div>
            </div>
        </template>

        <div class="flex justify-start" x-show="thinking" style="display:none">
            <div class="robo-bubble rounded-xl border border-line bg-armor2 px-4 py-2.5 text-sm text-ink/50">{{ __('site.speaking.mascot_name') }} думает…</div>
        </div>

        {{-- Сбой показываем отдельной плашкой, а не репликой робота --}}
        <div class="rounded-xl border border-sun/40 bg-sun/5 px-4 py-2.5 text-sm text-ink/70" x-show="error" style="display:none">
            <span x-text="error"></span>
        </div>
    </div>

    {{-- Управление --}}
    <div class="mt-6 flex flex-wrap items-center gap-3" x-show="!report" style="display:none">
        <x-ui.button
            x-show="supported"
            @click="toggleListening()"
            x-bind:disabled="thinking || speaking"
            x-text="listening ? 'Остановить' : 'Говорить'"
        />

        <form @submit.prevent="sendTyped()" class="flex flex-1 items-center gap-2" x-show="!supported || typedMode">
            <input
                type="text"
                x-model="typed"
                placeholder="Напишите ответ по-английски…"
                class="w-full rounded-lg border bg-armor2 px-4 py-2.5 text-sm text-ink placeholder:text-ink/30 border-line focus:border-brand focus:outline-none focus:ring-4 focus:ring-brand/15"
            >
            <x-ui.button type="submit" variant="outline">Отправить</x-ui.button>
        </form>

        <button
            type="button"
            x-show="supported"
            @click="typedMode = !typedMode"
            class="text-xs font-bold uppercase tracking-wider text-ink/50 underline hover:text-brand"
        >Ввести текстом</button>

        <x-ui.button
            variant="outline"
            @click="finish()"
            x-show="history.some(t => t.role === 'user')"
            x-bind:disabled="thinking"
        >Завершить и разобрать</x-ui.button>
    </div>

    {{-- Разбор --}}
    <template x-if="report">
        <div class="mt-8 space-y-4">
            <x-ui.card :hover="false">
                <h2 class="font-display text-xl font-semibold text-ink">Разбор разговора</h2>
                <p class="mt-2 text-ink/70" x-text="report.summary"></p>
            </x-ui.card>

            <template x-if="report.mistakes && report.mistakes.length">
                <x-ui.card :hover="false">
                    <h3 class="mb-3 font-bold text-ink">Ошибки в речи</h3>
                    <div class="space-y-3">
                        <template x-for="(m, i) in report.mistakes" :key="i">
                            <div class="rounded-lg border border-line p-3">
                                <p class="text-sm text-red-500 line-through" x-text="m.said"></p>
                                <p class="text-sm font-semibold text-green-600 dark:text-green-400" x-text="m.better"></p>
                                <p class="mt-1 text-xs text-ink/60" x-text="m.note"></p>
                            </div>
                        </template>
                    </div>
                </x-ui.card>
            </template>

            <template x-if="report.pronunciation && report.pronunciation.length">
                <x-ui.card :hover="false">
                    <h3 class="mb-1 font-bold text-ink">Произношение</h3>
                    <p class="mb-3 text-xs text-ink/50">
                        Это слова, которые распознаватель расслышал неуверенно — подсказка,
                        а не точная оценка произношения.
                    </p>
                    <div class="space-y-2">
                        <template x-for="(p, i) in report.pronunciation" :key="i">
                            <div class="flex items-start gap-3 rounded-lg border border-line p-3">
                                <button
                                    type="button"
                                    @click="pronounce(p.word, null)"
                                    class="mt-0.5 inline-flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-brand/10 text-brand hover:bg-brand hover:text-white"
                                    aria-label="Прослушать"
                                >
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5" /><path d="M15.54 8.46a5 5 0 0 1 0 7.07" /></svg>
                                </button>
                                <div>
                                    <p class="font-semibold text-ink" x-text="p.word"></p>
                                    <p class="text-xs text-ink/60" x-text="p.note"></p>
                                </div>
                            </div>
                        </template>
                    </div>
                </x-ui.card>
            </template>

            <x-ui.button @click="restart()">Новый разговор</x-ui.button>
        </div>
    </template>
</div>
@endsection

@push('scripts')
<script>
var MASCOT_NAME = @js(__('site.speaking.mascot_name'));

function speakingBot() {
    return {
        history: [],
        unclear: [],
        typed: '',
        typedMode: false,
        level: 'B1',
        listening: false,
        thinking: false,
        speaking: false,
        error: '',
        report: null,
        supported: false,
        recognition: null,
        robot: null,

        get statusText() {
            if (this.report) return 'Разговор завершён';
            if (this.thinking) return MASCOT_NAME + ' думает…';
            if (this.speaking) return MASCOT_NAME + ' говорит';
            if (this.listening) return 'Слушаю вас…';
            return 'Нажмите «Говорить» и скажите что-нибудь по-английски';
        },

        init: function () {
            this.robot = initPhilMascot(document.getElementById('robo-stage'));
            this.setupRecognition();
            this.greet();
        },

        // ---- Распознавание речи ----
        setupRecognition: function () {
            var Rec = window.SpeechRecognition || window.webkitSpeechRecognition;
            if (!Rec) { this.supported = false; this.typedMode = true; return; }

            this.supported = true;
            var self = this;
            var rec = new Rec();
            rec.lang = 'en-US';
            rec.interimResults = false;
            rec.continuous = false;
            rec.maxAlternatives = 1;

            rec.onresult = function (e) {
                var result = e.results[e.results.length - 1];
                var text = result[0].transcript.trim();

                // Слова из плохо распознанной реплики — кандидаты в проблемы
                // с произношением. Точной оценки браузер не даёт.
                if (typeof result[0].confidence === 'number' && result[0].confidence < 0.7) {
                    text.split(/\s+/).forEach(function (w) {
                        var clean = w.replace(/[^A-Za-z'-]/g, '');
                        if (clean.length > 2 && self.unclear.indexOf(clean) === -1) self.unclear.push(clean);
                    });
                }

                if (text) self.send(text);
            };

            rec.onerror = function () { self.listening = false; self.robot && self.robot.setState('idle'); };
            rec.onend = function () { self.listening = false; if (!self.speaking && !self.thinking) self.robot && self.robot.setState('idle'); };

            this.recognition = rec;
        },

        toggleListening: function () {
            if (!this.recognition) return;

            if (this.listening) {
                this.recognition.stop();
                this.listening = false;
                return;
            }

            // Микрофон и синтез речи одновременно дают эхо — робот услышал
            // бы сам себя, поэтому сначала замолкаем.
            window.speechSynthesis.cancel();
            this.speaking = false;

            try {
                this.recognition.start();
                this.listening = true;
                this.robot.setState('listening');
            } catch (e) { /* уже запущено */ }
        },

        // ---- Диалог ----
        greet: function () {
            var self = this;
            this.thinking = true;
            this.ask('/ielts/speaking/bot/reply', { history: [], level: this.level })
                .then(function (data) { self.receive(data); })
                .catch(function () { self.fail(); })
                .finally(function () { self.thinking = false; });
        },

        /**
         * Ответ сервера. Сбой (квота, таймаут) — это не реплика робота: если
         * положить её в историю, она уедет в контекст следующего запроса и в
         * разбор ошибок, где ученик увидит разбор чужого текста.
         */
        receive: function (data) {
            if (!data || data.error || !data.reply) {
                this.fail(data && data.message);

                return;
            }

            this.error = '';
            this.history.push({ role: 'bot', text: data.reply });
            this.scroll();
            this.say(data.reply);
        },

        fail: function (message) {
            this.error = message || 'Не удалось связаться с роботом. Попробуйте ещё раз через минуту.';
            this.robot.setState('idle');
        },

        sendTyped: function () {
            var text = (this.typed || '').trim();
            if (!text) return;
            this.typed = '';
            this.send(text);
        },

        send: function (text) {
            var self = this;
            this.history.push({ role: 'user', text: text });
            this.thinking = true;
            this.robot.setState('thinking');
            this.scroll();

            this.ask('/ielts/speaking/bot/reply', { history: this.history, level: this.level })
                .then(function (data) { self.receive(data); })
                .catch(function () { self.fail(); })
                .finally(function () { self.thinking = false; });
        },

        // ---- Голос робота ----
        say: function (text) {
            if (!('speechSynthesis' in window)) return;

            var self = this;
            window.speechSynthesis.cancel();

            var u = new SpeechSynthesisUtterance(text);
            u.lang = 'en-US';
            u.rate = 0.95;
            var voice = (typeof pickBestEnglishVoice === 'function') ? pickBestEnglishVoice() : null;
            if (voice) u.voice = voice;

            u.onstart = function () { self.speaking = true; self.robot.setState('speaking'); };
            u.onend = function () { self.speaking = false; self.robot.setState('idle'); };
            u.onerror = function () { self.speaking = false; self.robot.setState('idle'); };

            window.speechSynthesis.speak(u);
        },

        // ---- Итог ----
        finish: function () {
            var self = this;
            window.speechSynthesis.cancel();
            if (this.recognition) { try { this.recognition.stop(); } catch (e) {} }
            this.listening = false;
            this.thinking = true;
            this.robot.setState('thinking');

            this.ask('/ielts/speaking/bot/report', { history: this.history, unclear: this.unclear })
                .then(function (data) {
                    self.report = data;
                    self.robot.setState('idle');
                })
                .finally(function () { self.thinking = false; });
        },

        restart: function () {
            this.history = [];
            this.unclear = [];
            this.report = null;
            this.error = '';
            this.greet();
        },

        // ---- Вспомогательное ----
        ask: function (url, body) {
            return fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
                body: JSON.stringify(body),
            }).then(function (r) { return r.json(); });
        },

        scroll: function () {
            var el = this.$refs.log;
            if (el) setTimeout(function () { el.scrollIntoView({ block: 'end', behavior: 'smooth' }); }, 50);
        },
    };
}

/**
 * Включает CSS-анимацию маскота (см. components/phil-mascot.blade.php):
 * вся анимация решается через [data-state="…"] в CSS, здесь только
 * переключается атрибут на корневом элементе компонента.
 */
function initPhilMascot(container) {
    var el = container ? container.querySelector(".phil-mascot") : null;

    return {
        setState: function (next) { if (el) el.setAttribute("data-state", next); },
    };
}
</script>
@endpush
