{{--
    Голосовое приветствие Phil на входе в личный кабинет: «Добро пожаловать,
    {Имя}» + короткая сводка по прогрессу, озвученная через встроенный в
    браузер синтез речи (тот же механизм, что у произношения слов).

    Текст собирается на сервере из реальных данных пользователя, поэтому
    в озвучке не может появиться выдуманная статистика.

    Озвучивается один раз за сессию браузера. Браузеры блокируют автоплей
    звука до первого действия пользователя, поэтому если автозапуск не
    сработал — приветствие проговаривается при первом клике по странице,
    а кнопка «Прослушать» позволяет включить его вручную в любой момент.

    Когда реплика дозвучала до конца, карточка плавно исчезает.
--}}
@props([
    'stats' => [],
    'nextLesson' => null,
])

@php
    $user = auth()->user();
    $firstName = $user ? (explode(' ', trim($user->name))[0] ?: $user->name) : '';

    $streak = (int) ($stats['streak'] ?? 0);
    $wordsLearned = (int) ($stats['words_learned'] ?? 0);
    $lessonsCompleted = (int) ($stats['lessons_completed'] ?? 0);

    // Русские числительные: 1 день, 2 дня, 5 дней.
    $plural = function (int $n, string $one, string $few, string $many): string {
        $mod100 = $n % 100;
        $mod10 = $n % 10;

        if ($mod100 >= 11 && $mod100 <= 14) {
            return $many;
        }
        if ($mod10 === 1) {
            return $one;
        }
        if ($mod10 >= 2 && $mod10 <= 4) {
            return $few;
        }

        return $many;
    };

    // Каждая реплика — {text, lang}: английские названия уроков читает
    // английский голос, остальное — русский.
    $lines = [
        ['text' => "Добро пожаловать, {$firstName}!", 'lang' => 'ru'],
    ];

    if ($streak > 0) {
        $dayWord = $plural($streak, 'день', 'дня', 'дней');
        $lines[] = ['text' => "Вы занимаетесь {$streak} {$dayWord} подряд. Отличный ритм, продолжайте в том же духе.", 'lang' => 'ru'];
    } elseif ($lessonsCompleted > 0) {
        $lines[] = ['text' => 'Вы давно не заглядывали. Один урок сегодня — и серия занятий начнётся заново.', 'lang' => 'ru'];
    } else {
        $lines[] = ['text' => 'Вы только начинаете, и это самый интересный момент. Первый урок открыт прямо сейчас.', 'lang' => 'ru'];
    }

    if ($wordsLearned > 0) {
        $wordWord = $plural($wordsLearned, 'слово', 'слова', 'слов');
        $lines[] = ['text' => "В вашем словаре уже {$wordsLearned} выученных {$wordWord}.", 'lang' => 'ru'];
    }

    if ($nextLesson) {
        $lines[] = ['text' => 'Следующий урок называется', 'lang' => 'ru'];
        $lines[] = ['text' => $nextLesson->title.'.', 'lang' => 'en'];
    } else {
        $lines[] = ['text' => 'Все доступные уроки пройдены — впечатляющий результат.', 'lang' => 'ru'];
    }

    $lines[] = ['text' => 'Хорошей учёбы!', 'lang' => 'ru'];

    // В карточке приветствие уже вынесено в заголовок — под ним показываем
    // остальную часть реплики, чтобы текст не дублировался.
    $spokenText = collect($lines)->skip(1)->pluck('text')->join(' ');
@endphp

<div
    id="voice-greeting"
    class="mb-8 overflow-hidden rounded-2xl bg-navy shadow-soft"
    data-reveal
    data-lines="{{ json_encode($lines, JSON_UNESCAPED_UNICODE) }}"
>
    <div class="flex flex-wrap items-center gap-4 p-5 sm:p-6">
        <span class="grid h-12 w-12 shrink-0 place-items-center rounded-full bg-gold text-navy">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <circle cx="12" cy="12" r="10" /><path d="M8 14s1.5 2 4 2 4-2 4-2" /><line x1="9" y1="9" x2="9.01" y2="9" /><line x1="15" y1="9" x2="15.01" y2="9" />
            </svg>
        </span>

        <div class="min-w-0 flex-1">
            <p class="font-display text-lg font-semibold text-white">Добро пожаловать, {{ $firstName }}!</p>
            <p class="mt-0.5 text-sm leading-relaxed text-white/60">{{ $spokenText }}</p>
        </div>

        <div class="flex shrink-0 items-center gap-2">
            <button
                type="button"
                id="voice-greeting-play"
                class="inline-flex items-center gap-2 rounded border-2 border-gold px-4 py-2 text-xs font-bold uppercase tracking-wider text-gold transition hover:bg-gold hover:text-navy"
            >
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5" /><path d="M15.54 8.46a5 5 0 0 1 0 7.07" /><path d="M19.07 4.93a10 10 0 0 1 0 14.14" />
                </svg>
                <span id="voice-greeting-play-label">Прослушать</span>
            </button>

            <button
                type="button"
                id="voice-greeting-mute"
                class="grid h-9 w-9 place-items-center rounded text-white/60 transition hover:bg-white/10 hover:text-gold"
                aria-label="Отключить голосовое приветствие"
                title="Отключить голосовое приветствие"
            >
                <svg id="voice-greeting-mute-on" width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5" /><line x1="23" y1="9" x2="17" y2="15" /><line x1="17" y1="9" x2="23" y2="15" />
                </svg>
                <svg id="voice-greeting-mute-off" width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" style="display:none">
                    <polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5" /><path d="M15.54 8.46a5 5 0 0 1 0 7.07" />
                </svg>
            </button>
        </div>
    </div>
</div>

<script>
(function () {
    var root = document.getElementById('voice-greeting');
    if (!root || !('speechSynthesis' in window)) return;

    var MUTE_KEY = 'pe-voice-greeting-muted';
    var SESSION_KEY = 'pe-voice-greeting-played';

    var playBtn = document.getElementById('voice-greeting-play');
    var playLabel = document.getElementById('voice-greeting-play-label');
    var muteBtn = document.getElementById('voice-greeting-mute');
    var muteOnIcon = document.getElementById('voice-greeting-mute-on');
    var muteOffIcon = document.getElementById('voice-greeting-mute-off');

    var lines = [];
    try { lines = JSON.parse(root.getAttribute('data-lines')) || []; } catch (e) { return; }
    if (!lines.length) return;

    function storageGet(store, key) {
        try { return store.getItem(key); } catch (e) { return null; }
    }
    function storageSet(store, key, value) {
        try { store.setItem(key, value); } catch (e) {}
    }

    var muted = storageGet(localStorage, MUTE_KEY) === '1';

    function renderMuteState() {
        muteOnIcon.style.display = muted ? 'none' : '';
        muteOffIcon.style.display = muted ? '' : 'none';
        muteBtn.setAttribute('aria-label', muted ? 'Включить голосовое приветствие' : 'Отключить голосовое приветствие');
        muteBtn.setAttribute('title', muted ? 'Включить голосовое приветствие' : 'Отключить голосовое приветствие');
    }
    renderMuteState();

    // Голоса подгружаются асинхронно. Важно вешать слушатель через
    // addEventListener, а не onvoiceschanged — иначе затрём обработчик
    // из pronounce.js, который выбирает голос для произношения слов.
    function pickVoice(lang) {
        var voices = window.speechSynthesis.getVoices() || [];
        var matching = voices.filter(function (v) {
            return v.lang && v.lang.toLowerCase().indexOf(lang) === 0;
        });
        if (!matching.length) return null;

        var quality = [/google/i, /online/i, /neural/i, /natural/i];
        for (var i = 0; i < quality.length; i++) {
            var better = matching.find(function (v) { return quality[i].test(v.name); });
            if (better) return better;
        }
        return matching[0];
    }

    var speaking = false;
    var stoppedByUser = false;

    function setPlayingUi(isPlaying) {
        speaking = isPlaying;
        playLabel.textContent = isPlaying ? 'Остановить' : 'Прослушать';
    }

    function stop() {
        stoppedByUser = true;
        window.speechSynthesis.cancel();
        setPlayingUi(false);
    }

    // Приветствие своё дело сделало — убираем карточку, чтобы она не
    // занимала место в кабинете. Скрываем только после полностью
    // прозвучавшей реплики: если ученик сам нажал «Остановить», карточка
    // остаётся, чтобы можно было переслушать.
    function dismiss() {
        if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
            root.remove();
            return;
        }

        root.style.overflow = 'hidden';
        root.style.maxHeight = root.offsetHeight + 'px';
        root.style.transition = 'opacity .45s ease, transform .45s ease, max-height .45s ease, margin-bottom .45s ease';

        requestAnimationFrame(function () {
            root.style.opacity = '0';
            root.style.transform = 'translateY(-8px)';
            root.style.maxHeight = '0px';
            root.style.marginBottom = '0px';
        });

        setTimeout(function () { root.remove(); }, 550);
    }

    function speakAll() {
        if (speaking) { stop(); return; }

        stoppedByUser = false;
        window.speechSynthesis.cancel();
        setPlayingUi(true);

        lines.forEach(function (line, index) {
            var utterance = new SpeechSynthesisUtterance(line.text);
            var isRu = line.lang !== 'en';
            utterance.lang = isRu ? 'ru-RU' : 'en-US';
            utterance.rate = isRu ? 1 : 0.95;

            var voice = pickVoice(isRu ? 'ru' : 'en');
            if (voice) utterance.voice = voice;

            if (index === lines.length - 1) {
                utterance.onend = function () {
                    setPlayingUi(false);
                    // Chrome шлёт onend и на отменённые реплики, поэтому
                    // ориентируемся на флаг ручной остановки.
                    if (!stoppedByUser) setTimeout(dismiss, 700);
                };
                utterance.onerror = function () { setPlayingUi(false); };
            }

            window.speechSynthesis.speak(utterance);
        });
    }

    playBtn.addEventListener('click', function () {
        storageSet(sessionStorage, SESSION_KEY, '1');
        speakAll();
    });

    muteBtn.addEventListener('click', function () {
        muted = !muted;
        storageSet(localStorage, MUTE_KEY, muted ? '1' : '0');
        renderMuteState();
        if (muted) stop();
    });

    window.addEventListener('beforeunload', function () { window.speechSynthesis.cancel(); });

    // Автозапуск — один раз за сессию браузера и только если не отключено.
    if (muted || storageGet(sessionStorage, SESSION_KEY) === '1') return;

    function autoGreet() {
        if (storageGet(sessionStorage, SESSION_KEY) === '1') return;
        storageSet(sessionStorage, SESSION_KEY, '1');
        speakAll();
    }

    function attemptAutoplay() {
        autoGreet();

        // Если браузер заблокировал автоплей, речь не стартует — тогда
        // проигрываем приветствие при первом же действии пользователя.
        setTimeout(function () {
            if (window.speechSynthesis.speaking || window.speechSynthesis.pending) return;

            setPlayingUi(false);
            var onFirstGesture = function () {
                document.removeEventListener('pointerdown', onFirstGesture);
                document.removeEventListener('keydown', onFirstGesture);
                if (muted) return;
                speakAll();
            };
            document.addEventListener('pointerdown', onFirstGesture, { once: true });
            document.addEventListener('keydown', onFirstGesture, { once: true });
        }, 350);
    }

    // Голоса могут быть ещё не загружены на момент старта скрипта.
    if ((window.speechSynthesis.getVoices() || []).length) {
        attemptAutoplay();
    } else {
        var started = false;
        var startOnce = function () {
            if (started) return;
            started = true;
            attemptAutoplay();
        };
        window.speechSynthesis.addEventListener('voiceschanged', startOnce);
        setTimeout(startOnce, 1200);
    }
})();
</script>
